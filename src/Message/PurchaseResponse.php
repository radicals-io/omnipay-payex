<?php

namespace Omnipay\PayEx\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Stripe Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $statusCode;

    public function __construct($request, $data, $statusCode = 200)
    {
        parent::__construct($request, $data);
        $this->statusCode = $statusCode;
    }

    /**
     * Has the call to the processor succeeded?
     * When we need to redirect the browser we return false as the transaction is not yet complete
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * Should the user's browser be redirected?
     *
     * @return bool
     */
    public function isRedirect()
    {
        if ($this->data['status'] != '00') {
            return false;
        }

        $result = current($this->data['result']);
        return !empty($result['url']);
    }

    /**
     * Transparent redirect is the mode whereby a form is presented to the user that POSTs to the payment
     * processor site directly. If this returns true the site will need to provide a form for this
     *
     * @return bool
     */
    public function isTransparentRedirect()
    {
        return false;
    }

    public function getRedirectUrl()
    {
        $result = current($this->data['result']);
        return $result['url'];
    }

    /**
     * Should the browser redirect using GET or POST
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return $this->getData();
    }

    public function getMessage()
    {
        if(is_array($this->data['error'])){
            return implode(", ",$this->data['error']['message']);
        }else{
            return $this->data['error'];
        }
    }
}
