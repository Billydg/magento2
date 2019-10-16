<?php
/**
 * Copyright © 2019 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mollie\Payment\Service\Config;

use Magento\Quote\Model\Quote;
use Magento\Tax\Model\Calculation;
use Mollie\Payment\Config;

class PaymentFee
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Calculation
     */
    private $taxCalculation;

    public function __construct(
        Config $config,
        Calculation $taxCalculation
    ) {
        $this->config = $config;
        $this->taxCalculation = $taxCalculation;
    }

    public function includingTax(Quote $quote)
    {
        $method = $quote->getPayment()->getMethod();

        $amount = $this->getAmount($method, $quote->getStoreId());

        return (double)str_replace(',', '.', $amount);
    }

    public function excludingTax(Quote $quote)
    {
        $amount = $this->includingTax($quote);
        $tax = $this->tax($quote);

        return $amount - $tax;
    }

    public function tax(Quote $quote)
    {
        $shippingAddress = $quote->getShippingAddress();
        $billingAddress = $quote->getBillingAddress();
        $customerTaxClassId = $quote->getCustomerTaxClassId();
        $storeId = $quote->getStoreId();

        $request = $this->taxCalculation->getRateRequest(
            $shippingAddress,
            $billingAddress,
            $customerTaxClassId,
            $storeId
        );

        $taxClassId = $this->getTaxClass($quote);
        $request->setProductClassId($taxClassId);

        $rate = $this->taxCalculation->getRate($request);

        $fee = $this->includingTax($quote);
        $result = $this->taxCalculation->calcTaxAmount(
            $fee,
            $rate,
            true,
            false
        );

        return $result;
    }

    public function isAvailableForMethod(Quote $quote)
    {
        $method = $quote->getPayment()->getMethod();

        return in_array($method, ['mollie_methods_klarnapaylater', 'mollie_methods_klarnasliceit']);
    }

    private function getTaxClass(Quote $quote)
    {
        $method = $quote->getPayment()->getMethod();

        if ($method == 'mollie_methods_klarnapaylater') {
            return $this->config->klarnaPaylaterPaymentSurchargeTaxClass($quote->getStoreId());
        }

        if ($method == 'mollie_methods_klarnasliceit') {
            return $this->config->klarnaSliceitPaymentSurchargeTaxClass($quote->getStoreId());
        }
    }

    private function getAmount($method, $storeId)
    {
        if ($method == 'mollie_methods_klarnapaylater') {
            return $this->config->klarnaPaylaterPaymentSurcharge($storeId);
        }

        if ($method == 'mollie_methods_klarnasliceit') {
            return $this->config->klarnaSliceitPaymentSurcharge($storeId);
        }

        return 0;
    }
}
