<?php

namespace Interteleco\SMSBox\Controller\Adminhtml\Test;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Interteleco_SMSBox::smsbox');
        $resultPage->getConfig()->getTitle()->prepend(__('SMSBox Test SMS'));

        return $resultPage;
    }
}
