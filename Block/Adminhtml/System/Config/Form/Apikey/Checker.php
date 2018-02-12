<?php
/**
 * Copyright © 2018 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mollie\Payment\Block\Adminhtml\System\Config\Form\Apikey;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Checker
 *
 * @package Mollie\Payment\Block\Adminhtml\System\Config\Form\Apikey
 */
class Checker extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Mollie_Payment::system/config/button/apikey.phtml';
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Checker constructor.
     *
     * @param Context $context
     * @param array   $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        $this->request = $context->getRequest();
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('mollie/action/apikey');
    }

    /**
     * @return mixed
     */
    public function getButtonHtml()
    {
        try {
            $buttonData = ['id' => 'apikey_button', 'label' => __('Test Apikey')];
            $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData($buttonData);
            return $button->toHtml();
        } catch (\Exception $e) {
            return '';
        }
    }
}
