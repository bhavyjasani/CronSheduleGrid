<?php
namespace Dolphin\CronGrid\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Cron\Model\Schedule;

class CronStatus extends AbstractSource
{
    /**
     * Get an associative array of available cron statuses.
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            Schedule::STATUS_PENDING => __('Pending'),
            Schedule::STATUS_RUNNING => __('Running'),
            Schedule::STATUS_SUCCESS => __('Success'),
            Schedule::STATUS_MISSED  => __('Missed'),
            Schedule::STATUS_ERROR   => __('Error'),
        ];
    }

    /**
     * Get all options as an array with 'value' and 'label' keys.
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = [];
        foreach ($this->getOptionArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $options;
    }

    /**
     * Get the option text for a given value.
     *
     * @param string $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getOptionArray();
        return isset($options[$value]) ? $options[$value] : false;
    }
}
