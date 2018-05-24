<?php

namespace Interteleco\SMSBox\Observer;

use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer       as Observer;
use \Magento\Framework\View\Element\Context as Context;
use \Magento\Framework\Exception\LocalizedException;
use \Interteleco\SMSBox\Helper\Data as Helper;

class ChangedConfiguration implements ObserverInterface
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
     * Helper
     *
     * @var \Interteleco\SMSBox\Helper\Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        Helper $helper
    ) {
        $this->request = $context->getRequest();
        $this->layout  = $context->getLayout();
        $this->helper  = $helper;
    }

    /**
     * The execute class
     *
     * @param  Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $observer->getEvent()->getName();
        $result = $this->helper->verifyApi();
        if ($result === false) {
            $this->helper->setConfigEmpty();
            throw new LocalizedException(
                __(
                    "error in username and/or password is incorrect and/or customer id is invalid"
                )
            );
        }
    }
}
