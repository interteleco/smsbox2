<?php

namespace Interteleco\SMSBox\Model\ResourceModel\History;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init(
            'Interteleco\SMSBox\Model\History',
            'Interteleco\SMSBox\Model\ResourceModel\History'
        );
    }
}
