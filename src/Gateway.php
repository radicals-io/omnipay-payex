<?php

namespace Omnipay\PayEx;

use Omnipay\Common\AbstractGateway;

/**
 * Store Check payment gateway
 *
 * This is an example of a custom gateway. It simply extends the existing
 * Omnipay Manual payment gateway.
 *
 * For more information about developing custom gateways, please see
 * https://github.com/omnipay/omnipay
 */

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'PayEx';
    }

    public function getDefaultParameters()
    {
        return array(
            'username' => '',
            'secret' => '',
            'testMode' => false,
        );
    }

    public function authentication()
    {

    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\PayEx\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Billplz\Message\PurchaseRequest', $parameters);
    }

    public function getAccountNumber()
    {
        return $this->getParameter('accountNumber');
    }

    public function setAccountNumber($value)
    {
        return $this->setParameter('accountNumber', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }
}
