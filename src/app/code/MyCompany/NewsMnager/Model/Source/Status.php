<?php

namespace MyCompany\NewsManager\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface

{
  const STATUS_ENABLED = 1;
  const STATUS_DISABLED = 0;

  public function ToOptionArray()
  {
    return [
      ['vlaue' => self::STATUS_ENABLED, 'label' => __('Enabled')],
      ['value' => self::STATUS_DISABLED],
      'label' => __('Disabled')
    ];
  }
}
