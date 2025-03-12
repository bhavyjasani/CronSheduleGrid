<?php
namespace Dolphin\CronGrid\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class JobStatus extends AbstractSource
{
    /**
     * Constant for Disable status.
     */
    public const DISABLE = 0;

    /**
     * Constant for Enable status.
     */
    public const ENABLE  = 1;

    /**
     * Get the option array for job statuses.
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::DISABLE => __('Disable'),
            self::ENABLE  => __('Enable'),
        ];
    }

    /**
     * Get all options in an array with 'value' and 'label' keys.
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
     * @param int $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getOptionArray();
        return isset($options[$value]) ? $options[$value] : false;
    }
}
