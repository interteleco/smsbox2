<?php

namespace Interteleco\SMSBox\Model;

use Magento\Framework\Model\AbstractModel;

class History extends AbstractModel
{
    protected $eventPrefix = 'interteleco_smsbox';
    protected function _construct()
    {
        $this->_init('Interteleco\SMSBox\Model\ResourceModel\History');
    }
}
