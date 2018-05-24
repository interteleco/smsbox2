<?php

namespace Interteleco\SMSBox\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer       as Observer;
use \Magento\Framework\View\Element\Context as Context;
use \Magento\Framework\App\ObjectManager as ObjectManager;

class SendNewSms implements ObserverInterface
{

    /**
     * Https request
     *
     * @var \Zend\Http\Request
     */
    protected $request;
    /**
     * Layout Interface
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;
    /**
     * Constructor
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->request = $context->getRequest();
        $this->layout  = $context->getLayout();
    }
    /**
     * The execute class
     *
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $resultArray   = $observer->getEvent()->getResult();
        $objectManager = ObjectManager::getInstance();
        $historyObject = $objectManager->create(
            'Interteleco\SMSBox\Model\History'
        );
        $historyObject->setStatus($resultArray['flag']);
        $historyObject->setResponse($resultArray['response']);
        $historyObject->setSentAt(time());
        $historyObject->setNumber($resultArray['phone']);
        $historyObject->setSender($resultArray['sender_id']);
        $historyObject->setType($resultArray['type']);
        if ($resultArray['type'] === 'New Order') {
            $historyObject->setOrderId($resultArray['order_id']);
        }
        $historyObject->setMessage($resultArray['message']);
        $historyObject->setIsObjectNew(true);
        $historyObject->save();
    }
}
