<?php

namespace Interteleco\SMSBox\Observer;

use Magento\Framework\Event\ManagerInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer       as Observer;
use \Magento\Framework\View\Element\Context as Context;
use \Interteleco\SMSBox\Helper\Data as Helper;

class NewCoupon implements ObserverInterface
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
     * Username
     *
     * @var $customer
     */
    protected $customer;
    /**
     * Username
     *
     * @var $username
     */
    protected $username;
    /**
     * Password
     *
     * @var $password
     */
    protected $password;
    /**
     * customerId
     *
     * @var $customerId
     */
    protected $customerId;
    /**
     * Sender ID
     *
     * @var $senderId
     */
    protected $senderId;
    /**
     * Phone
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
     * @param Customer         $customers
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        Context $context,
        Helper $helper,
        Customer $customers,
        ManagerInterface $eventManager
    ) {
        $this->request = $context->getRequest();
        $this->layout  = $context->getLayout();
        $this->helper  = $helper;
        $this->customer = $customers;
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

        $rule = $observer->getEvent()->getRule();
        if ($this->helper->isCustomerNotificationsOnNewCouponStatus() == 1
            && $rule->isObjectNew()
            && $this->helper->getSmsboxApiCustomerId() != null
            && $this->helper->getSmsboxApiCustomerId() != ""
        ) {
            $couponData          =   [
                'couponName' =>  $rule->getName(),
                'couponCode' =>  $rule->getCouponCode()
            ];

            $this->message  =
                $this->helper->isCustomerNotificationsOnNewCouponMessage();

            $this->message  = str_replace(
                ['{couponName}', '{couponCode}'],
                $couponData,
                $this->message
            );

            $this->senderId =
                $this->helper->isCustomerNotificationsOnNewCouponSenderId();

            $this->phone    =
                $this->getFilteredCustomerCollection(
                    $rule->getCustomerGroupIds()
                );

            foreach ($this->phone as $phone) {
                $result = $this->helper->sendSms(
                    $this->senderId,
                    $phone,
                    $this->message,
                    'New Coupon'
                );
                $this->eventManager->dispatch(
                    'smsbox_on_send_new_sms',
                    ['result' => $result]
                );
            }
        }
    }
    private function getFilteredCustomerCollection($groupIds)
    {
        $collection = $this->customer->getCollection()
            ->addAttributeToFilter("group_id", $groupIds);

        $result = [];
        foreach ($collection as $customer) {
            $result[] =
                $customer->getDefaultBillingAddress(
                    $customer->getId()
                )->getTelephone();
        }
        return $result;
    }
}
