<?php
namespace Dolphin\CronGrid\Model;

use Magento\Framework\Shell;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Process\PhpExecutableFinderFactory;
use Magento\Cron\Model\Schedule;
use Magento\Framework\App\ResourceConnection;

/**
 * Class SpecificJobRun
 *
 * Handles the execution of a specific cron job, including creating a schedule record
 * and running the job via shell.
 */
class SpecificJobRun
{
    /**
     * @var Shell
     */
    private $shell;

    /**
     * @var PhpExecutableFinderFactory
     */
    private $phpExecutableFinder;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * SpecificJobRun constructor.
     *
     * @param Shell $shell
     * @param PhpExecutableFinderFactory $phpExecutableFinderFactory
     * @param ResourceConnection $resource
     */
    public function __construct(
        Shell $shell,
        PhpExecutableFinderFactory $phpExecutableFinderFactory,
        ResourceConnection $resource
    ) {
        $this->shell = $shell;
        $this->phpExecutableFinder = $phpExecutableFinderFactory->create();
        $this->resource = $resource;
    }

    /**
     * Run the specific cron job.
     *
     * @param string $jobCode The job code to run.
     * @throws LocalizedException If there is an error while executing the job.
     */
    public function runJob($jobCode)
    {
        try {
            $connection = $this->resource->getConnection();
            $scheduleTable = $this->resource->getTableName('cron_schedule');
            $connection->insert(
                $scheduleTable,
                [
                    'job_code' => $jobCode,
                    'status' => Schedule::STATUS_PENDING,
                    'created_at' => date('Y-m-d H:i:s'),
                    'scheduled_at' => date('Y-m-d H:i:s')
                ]
            );
            $phpPath = $this->phpExecutableFinder->find() ?: 'php';
            $this->shell->execute(
                '%s %s cron:run --group=%s',
                [
                    $phpPath,
                    BP . '/bin/magento',
                    $jobCode
                ]
            );
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Failed to run job %1: %2', $jobCode, $e->getMessage())
            );
        }
    }
}
