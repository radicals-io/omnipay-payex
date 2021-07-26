<?php

namespace Omnipay\PayEx\Message;

use Omnipay\Common\Message\AbstractResponse;

class CompletePurchaseResponse extends AbstractResponse
{
    protected $statusCode;

    protected $status;

    public function __construct($request, $data, $statusCode = 200)
    {
        parent::__construct($request, $data);
        $this->statusCode = $statusCode;

        $this->status = false;
        if ($this->data[0]['status'] == 'Sales') {
            $this->status = true;
        }
    }

    public function isSuccessful()
    {
        return $this->status;
    }
}
