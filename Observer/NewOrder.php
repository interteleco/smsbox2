<?php

namespace Interteleco\SMSBox\Observer;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer       as Observer;
use \Magento\Framework\View\Element\Context as Context;
use \Interteleco\SMSBox\Helper\Data as Helper;

class NewOrder implements ObserverInterface
{

    /**
     * Https request
     *
     * @var \Zend\Http\Request
     */
    protected $request;
    /**
     * Core event manager proxy
     *
     * @var ManagerInterface
     */
    protected $eventManager = null;
    /**
     * Layout Interface
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;
    /**
     * Data
     *
     * @var $helper
     */
    protected $helper;
    /**
     * Sender ID
     *
     * @var $senderId
     */
    protected $senderId;
    /**
     * Destination
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
     * @param ManagerInterface $eventManager
     * @param Context          $context
     * @param Helper           $helper       _helper
     */
    public function __construct(
        ManagerInterface $eventManager,
        Context $context,
        Helper $helper
    ) {
        $this->eventManager = $eventManager;
        $this->request = $context->getRequest();
        $this->layout  = $context->getLayout();
        $this->helper  = $helper;
    }

    /**
     * The execute class
     *
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->isCustomerNotificationsOnOrderStatus() == 1
            && $this->helper->getSmsboxApiCustomerId() != null
            && $this->helper->getSmsboxApiCustomerId() != ""
        ) {
            $order = $observer->getEvent()->getOrder();
            $orderData          =   [
                'order_id'      =>  $order->getIncrementId(),
                'firstname'     =>  $order->getCustomerFirstname(),
                'lastname'      =>  $order->getCustomerLastname(),
                'totalPrice'    =>  number_format($order->getGrandTotal(), 2)
            ];

            $this->message  =
                $this->helper->isCustomerNotificationsOnOrderMessage();

            $this->message  = $this->helper->manipulateSMS(
                $this->message,
                $orderData
            );

            $this->senderId =
                $this->helper->isCustomerNotificationsOnOrderSenderId();

            $this->phone    = $order->getBillingAddress()->getTelephone();

            $result = $this->helper->sendSms(
                $this->senderId,
                $this->phone,
                $this->message,
                'New Order'
            );
            $result['order_id'] = $order->getIncrementId();
            $this->eventManager->dispatch(
                'smsbox_on_send_new_sms',
                ['result' => $result]
            );
        }
    }
}
