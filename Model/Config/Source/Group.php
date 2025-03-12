<?php

namespace Dolphin\CronGrid\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Group extends AbstractSource
{
    /**
     * Returns an associative array of available group options.
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            'default' => __('Default'),
            'index' => __('Index'),
            'catalog' => __('Catalog'),
            'sales' => __('Sales'),
            'customers' => __('Customers')
        ];
    }

    /**
     * Returns all available options in an array of arrays with 'value' and 'label' keys.
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
     * Returns the option text for a given value.
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
