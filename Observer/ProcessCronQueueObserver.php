<?php

namespace Dolphin\CronGrid\Observer;

use Laminas\Http\PhpEnvironment\Request as Environment;
use DateTime as DateTimeInterface;
use DateTimeZone;
use Exception;
use Magento\Cron\Model\ConfigInterface;
use Magento\Cron\Model\DeadlockRetrierInterface;
use Magento\Cron\Model\Schedule;
use Magento\Cron\Model\ScheduleFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Console\Request;
use Magento\Framework\App\State;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Lock\LockManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Process\PhpExecutableFinderFactory;
use Magento\Framework\Profiler\Driver\Standard\StatFactory;
use Magento\Framework\ShellInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Dolphin\CronGrid\Model\ResourceModel\DolphinCron\CollectionFactory as Dolphincron;

/**
 * Observer for processing cron queue
 */
class ProcessCronQueueObserver extends \Magento\Cron\Observer\ProcessCronQueueObserver
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var Dolphincron
     */
    private $dolphincron;

    /**
     * Constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param ScheduleFactory $scheduleFactory
     * @param CacheInterface $cache
     * @param ConfigInterface $config
     * @param ScopeConfigInterface $scopeConfig
     * @param Request $request
     * @param ShellInterface $shell
     * @param DateTime $dateTime
     * @param PhpExecutableFinderFactory $phpExecutableFinderFactory
     * @param LoggerInterface $logger
     * @param State $state
     * @param StatFactory $statFactory
     * @param LockManagerInterface $lockManager
     * @param ManagerInterface $eventManager
     * @param DeadlockRetrierInterface $retrier
     * @param TimezoneInterface $localeDate
     * @param ResolverInterface $localeResolver
     * @param Dolphincron $dolphincron
     * @param Environment $environment
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ScheduleFactory $scheduleFactory,
        CacheInterface $cache,
        ConfigInterface $config,
        ScopeConfigInterface $scopeConfig,
        Request $request,
        ShellInterface $shell,
        DateTime $dateTime,
        PhpExecutableFinderFactory $phpExecutableFinderFactory,
        LoggerInterface $logger,
        State $state,
        StatFactory $statFactory,
        LockManagerInterface $lockManager,
        ManagerInterface $eventManager,
        DeadlockRetrierInterface $retrier,
        TimezoneInterface $localeDate,
        ResolverInterface $localeResolver,
        Dolphincron $dolphincron,
        Environment $environment
    ) {
        $this->localeDate = $localeDate;
        $this->locale     = $localeResolver->getLocale();
        $this->dolphincron = $dolphincron;
        parent::__construct(
            $objectManager,
            $scheduleFactory,
            $cache,
            $config,
            $scopeConfig,
            $request,
            $shell,
            $dateTime,
            $phpExecutableFinderFactory,
            $logger,
            $state,
            $statFactory,
            $lockManager,
            $eventManager,
            $retrier,
            $environment
        );
    }

    /**
     * Save cron schedule
     *
     * @param string $jobCode
     * @param string $cronExpression
     * @param int $timeInterval
     * @param bool $exists
     * @return void
     */
    protected function saveSchedule($jobCode, $cronExpression, $timeInterval, $exists)
    {
        if ($this->getvisibil($jobCode) == 0) {
            return;
        }
        
        $modifiedExpression = $this->getmodify($jobCode);
        
        if (!empty($modifiedExpression)) {
            $cronExpression = $modifiedExpression;
        }

        parent::saveSchedule($jobCode, $cronExpression, $timeInterval, $exists);
    }

    /**
     * Get visibility of a job
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

        return $collection->getData()[0]['visibility'];
    }

    /**
     * Get modified cron expression for a job
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

        return $collection->getData()[0]['modify_schedule'];
    }
}
