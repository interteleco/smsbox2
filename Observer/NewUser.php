<?php

namespace Interteleco\SMSBox\Observer;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer       as Observer;
use \Magento\Framework\View\Element\Context as Context;
use \Interteleco\SMSBox\Helper\Data as Helper;

class NewUser implements ObserverInterface
{

    /**
     * Core event manager proxy
     *
     * @var ManagerInterface
     */
    protected $eventManager = null;
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
     * Helper
     *
     * @var \Interteleco\SMSBox\Helper\Data
     */
    protected $helper;
    /**
     * Sender ID
     *
     * @var $senderId
     */
    protected $senderId;
    /**
     * phone
     *
     * @var $phone
     */
    protected $phone;
    /**
     * Message
     *
     * @var $message
     */
    protected $message;
    /**
     * Constructor
     *
     * @param Context          $context
     * @param Helper           $helper
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        Context $context,
        Helper $helper,
        ManagerInterface $eventManager
    ) {
        $this->helper  = $helper;
        $this->request = $context->getRequest();
        $this->layout  = $context->getLayout();
        $this->eventManager = $eventManager;
    }
    /**
     * The execute class
     *
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->isCustomerNotificationsOnRegisterStatus() == 1
            && $this->helper->getSmsboxApiCustomerId() != null
            && $this->helper->getSmsboxApiCustomerId() != ""
        ) {
            $event = $observer->getEvent();
            $customer = [
                'firstname' =>$event->getCustomer()->getFirstname(),
                'lastname'  =>$event->getCustomer()->getLastname()
            ];
            $this->message  = $this->helper
                ->isCustomerNotificationsOnRegisterMessage();

            $this->message  = str_replace(
                ['{firstname}', '{lastname}'],
                $customer,
                $this->message
            );
            $this->senderId = $this->helper
                ->isCustomerNotificationsOnRegisterSenderId();

            $this->phone    = $this->request->getPost('telephone');
            $result = $this->helper->sendSms(
                $this->senderId,
                $this->phone,
                $this->message,
                'New User'
            );
            $this->eventManager->dispatch(
                'smsbox_on_send_new_sms',
                ['result' => $result]
            );
        }
    }
}
