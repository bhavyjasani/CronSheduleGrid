<?php

namespace Dolphin\CronGrid\Ui\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Cron\Model\ResourceModel\Schedule\Collection as ScheduleCollection;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;
use Magento\Cron\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Dolphin\CronGrid\Model\ResourceModel\DolphinCron\CollectionFactory as Dolphincron;

class JobProvider extends AbstractDataProvider
{
    /**
     * @var int Size
     */
    protected $size = 20;
    /**
     * @var int Offset
     */
    protected $offset = 1;
    /**
     * @var array Filters
     */
    protected $filters = [];
    /**
     * @var string Sort field name
     */
    protected $sortField = 'name';
    /**
     * @var string Sort direction
     */
    protected $sortDir = 'asc';
    /**
     * @var ScheduleCollectionFactory
     */
    private $scheduleCollectionFactory;
    /**
     * @var mixed Loaded jobs
     */
    private $loadedJobs = null;
    /**
     * @var Config
     */
    private $cronConfig;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var Dolphincron
     */
    private $dolphincron;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Dolphincron $dolphincron
     * @param ScheduleCollectionFactory $scheduleCollectionFactory
     * @param Config $cronConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Dolphincron $dolphincron,
        ScheduleCollectionFactory $scheduleCollectionFactory,
        Config $cronConfig,
        ScopeConfigInterface $scopeConfig,
        array $meta = [],
        array $data = []
    ) {
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->cronConfig = $cronConfig;
        $this->scopeConfig = $scopeConfig;
        $this->dolphincron = $dolphincron;
        
        // Initialize the collection property to prevent getAllIds() on null error
        $this->collection = $this->dolphincron->create();
        
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Add a filter for the job data
     *
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[$filter->getConditionType()] = [
            'field' => $filter->getField(),
            'value' => $filter->getValue()
        ];
    }

    /**
     * Add sorting order
     *
     * @param string $field
     * @param string $direction
     */
    public function addOrder($field, $direction)
    {
        $this->sortField = $field;
        $this->sortDir = strtolower($direction);
    }

    /**
     * Set the limit for data fetching
     *
     * @param int $offset
     * @param int $size
     */
    public function setLimit($offset, $size)
    {
        $this->size = $size;
        $this->offset = $offset;
    }

    /**
     * Retrieve and prepare jobs data
     *
     * @return array
     */
    private function getJobs()
    {
        if ($this->loadedJobs === null) {
            $collection = $this->dolphincron->create();
            // $collection->addFieldToSelect(['job_code', 'scheduled_at' , 'modify_schedule' , 'visibility'])
            //         ->setOrder('scheduled_at', 'DESC')
            //         ->getSelect()
            //         ->group('job_code');

            $cronJobs = $this->cronConfig->getJobs();
            $jobs = [];
            
            foreach ($collection as $schedule) {
                $jobCode = $schedule->getCronCode();
                $instancePath = '';
                $method = '';
                $cronExpr = '';
                $group = 'default';
                $visibility = $this->getvisibil($jobCode);
                $modify = $this->getmodify($jobCode);
                // $id = $schedule->getId();
                
                // Find instance, method and cron expression from configuration
                foreach ($cronJobs as $groupName => $groupJobs) {
                    if (isset($groupJobs[$jobCode])) {
                        $instancePath = isset($groupJobs[$jobCode]['instance'])
                            ? $groupJobs[$jobCode]['instance']
                            : '';
                        $method = isset($groupJobs[$jobCode]['method'])
                            ? $groupJobs[$jobCode]['method']
                            : '';
                        $cronExpr = isset($groupJobs[$jobCode]['schedule'])
                            ? $groupJobs[$jobCode]['schedule']
                            : '* * * * *';
                        $group = $groupName;
                        break;
                    }
                }
                
                $jobs[] = [
                    'id' => $schedule->getId(),
                    'name' => $jobCode,
                    'group' => $group,
                    'instance' => $instancePath,
                    'method' => $method,
                    'schedule' => $cronExpr,
                    'modify_schedule' =>$modify,
                    'visibility' => $visibility
                ];

                // $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/testlog.log');
                // $logger = new \Zend_Log();
                // $logger->addWriter($writer);
                // $logger->info('testt'); // Print string type data
                // $logger->info('Data::' . print_r($jobs, true));

            }
            
            $this->loadedJobs = $jobs;
        }

        return $this->loadedJobs;
    }

    /**
     * Extract group from job code
     *
     * @param string $jobCode
     * @return string
     */
    private function extractGroupFromJobCode($jobCode)
    {
        $parts = explode('_', $jobCode);
        return count($parts) > 1 ? $parts[0] : 'default';
    }

    /**
     * Retrieve data based on filters and limits
     *
     * @return array
     */
    public function getData()
    {
        $items = $this->getJobs();

        // add filter
        foreach ($this->filters as $type => $filter) {
            $field = $filter['field'];
            $value = str_replace('\\', '', $filter['value']);

            $items = array_filter($items, function ($item) use ($field, $value, $type) {
                switch ($type) {
                    case 'like':
                        return stripos($item[$field], substr($value, '1', '-1')) !== false;
                    case 'eq':
                        return $item[$field] === $value;
                    case 'in':
                        return in_array($item[$field], array_values($value), true);
                    default:
                        return true;
                }
            });
        }

        // add order
        $sortField = $this->sortField;
        $sortDir = $this->sortDir;
        usort($items, function ($a, $b) use ($sortField, $sortDir) {
            return $sortDir === 'asc' ? $a[$sortField] <=> $b[$sortField] : $b[$sortField] <=> $a[$sortField];
        });

        $totalRecords = count($items);

        // set limit
        $items = array_slice($items, ($this->offset - 1) * $this->size, $this->size);

        return compact('totalRecords', 'items');
    }

    /**
     * Get visibility for a job
     *
     * @param string $job
     * @return mixed
     */
    public function getvisibil($job)
    {

        $collection = $this->dolphincron->create();
    
        $collection->addFieldToFilter(
            'cron_code',
            ['eq' => $job]
        );

        // $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/testlog.log');
        // $logger = new \Zend_Log();
        // $logger->addWriter($writer);
        // $logger->info('testt');
        // $logger->info('Data::' . print_r($collection->getData()[0]['visibility'], true));

        return $collection->getData()[0]['visibility'];
    }

    /**
     * Get modify schedule for a job
     *
     * @param string $job
     * @return mixed
     */
    public function getmodify($job)
    {

        $collection = $this->dolphincron->create();
    
        $collection->addFieldToFilter(
            'cron_code',
            ['eq' => $job]
        );

        // $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/testlog.log');
        // $logger = new \Zend_Log();
        // $logger->addWriter($writer);
        // $logger->info('testt');
        // $logger->info('Data::' . print_r($collection->getData(), true));

        return $collection->getData()[0]['modify_schedule'];
    }
}
