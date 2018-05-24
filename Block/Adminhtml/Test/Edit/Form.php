<?php

namespace Interteleco\SMSBox\Block\Adminhtml\Test\edit;

use Interteleco\SMSBox\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Form extends Generic
{

    /**
     * $helper for SMSBox Module
     *
     * @var \Interteleco\SMSBox\Helper\Data
     */
    protected $helper;

    /**
     * @param Context     $context
     * @param Registry    $registry
     * @param FormFactory $formFactory
     * @param Data        $_helper
     * @param array       $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Data $_helper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->helper = $_helper ;
    }
    /**
     * Prepare form
     *
     * @return \Magento\Backend\Block\Widget\Form\Generic
     */
    protected function _prepareForm()
    {
        $result     = $this->helper->getInformation();
        $netPoints = $result['points']. ' Points';
        $senders    = $result['senders'];
        $disabled   = $result['status'] === true ? false : true;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );
        $form->setUseContainer(true);
        $fieldSet = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => 'SMSBox Test Api'
            ]
        );
        $fieldSet->addField(
            'phone',
            'text',
            [
                'name'  => 'phone_number',
                'label' => 'phone number',
                'title' => 'phone number',
                'required' => true,
                'validate-length' => true,
                'disabled' => $disabled,
                'class' => 'required-entry validate-digits',
            ]
        );
        $fieldSet->addField(
            'sender_id',
            'select',
            [
                'name' => 'sender_id',
                'label' => 'Sender id',
                'title' => 'Sender id',
                'required' => true,
                'class' => 'required-entry',
                'disabled' => $disabled,
                'options' => $senders,
            ]
        );
        $fieldSet->addField(
            'lang_text',
            'select',
            [
                'label' => __('Test Language'),
                'title' => __('Test Language'),
                'name' => 'lang_test',
                'required' => true,
                'disabled' => $disabled,
                'class' => 'required-entry',
                'options' => [
                    null => __('Select language for test sms'),
                    'en' => __('Test English Language'),
                    'ar' => __('Test Arabic Language')
                ],
            ]
        );
        $fieldSet->addField(
            'net_points',
            'label',
            [
                'label' => __('Net points'),
                'title' => __('Net points'),
                'bold' => true
            ]
        );
        $form->setValues(['net_points' => $netPoints]);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
