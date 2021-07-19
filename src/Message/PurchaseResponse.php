<?php

namespace Omnipay\IPay88\Message;

use Omnipay\Common\Message\AbstractResponse;

class PurchaseResponse extends AbstractResponse
{
    protected $endpoint = 'https://payment.ipay88.com.my/epayment/entry.asp';

    public function getTransactionId()
    {
        return $this->data['RefNo'];
    }

    public function isTransparentRedirect()
    {
        return true;
    }

    public function isRedirect()
    {
        return true;
    }

    public function isSuccessful()
    {
        return false;
    }

    public function getRedirectUrl()
    {
        return $this->endpoint;
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return $this->data;
    }

}