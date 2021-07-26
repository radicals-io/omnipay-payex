<?php

namespace Omnipay\PayEx\Message;

/**
 * Authorize Request
 */
class CompletePurchaseRequest extends AbstractRequest
{
    protected function createResponse($data, $statusCode)
    {
        return $this->response = new CompletePurchaseResponse($this, $data, $statusCode);
    }

    public function getHttpMethod()
    {
        return 'GET';
    }

    public function getAPI()
    {
        return 'Transactions/' . $this->getTxnId();
    }

    public function getData()
    {
        return [];
    }
}
