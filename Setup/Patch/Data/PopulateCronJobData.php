<?php
namespace Dolphin\CronGrid\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cron\Model\ConfigInterface;

class PopulateCronJobData implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    
    /**
     * @var ConfigInterface
     */
    private $cronConfig;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ConfigInterface $cronConfig
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ConfigInterface $cronConfig
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->cronConfig = $cronConfig;
    }

    /**
     * Apply function
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        
        // Get list of all cron jobs
        $cronJobs = $this->getAllCronJobs();
        
        // Prepare data to insert
        $data = [];
        foreach ($cronJobs as $jobCode) {
            $data[] = [
                'cron_code' => $jobCode,
                'modify_schedule' => '',  // Default empty value
                'visibility' => 1         // Default visibility
            ];
        }
        
        // Insert data into the table
        if (!empty($data)) {
            $this->moduleDataSetup->getConnection()->insertMultiple(
                $this->moduleDataSetup->getTable('Dolphin_Cron'),
                $data
            );
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * Get all cron job codes from the system
     *
     * @return array
     */
    private function getAllCronJobs()
    {
        $jobs = [];
        $cronJobs = $this->cronConfig->getJobs();
        
        foreach ($cronJobs as $group) {
            foreach ($group as $jobCode => $jobConfig) {
                $jobs[] = $jobCode;
            }
        }
        
        return array_unique($jobs);
    }

    /**
     * GetDependencies function
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * GetAliases function
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
