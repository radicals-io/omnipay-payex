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
            'testMode' => false
        );
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\PayEx\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayEx\Message\PurchaseRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\PayEx\Message\CompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayEx\Message\CompletePurchaseRequest', $parameters);
    }

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function verifySignature($responseData)
    {
        if (empty($responseData['signature'])) {
            return false;
        }

        $secret = $this->getSecret();
        return $responseData['signature'] == hash('sha512', $secret . '|' . $responseData['txn_id']);
    }
}
