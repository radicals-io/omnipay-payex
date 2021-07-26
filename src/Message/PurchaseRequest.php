<?php

namespace Omnipay\PayEx\Message;

/**
 * Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    protected function createResponse($data, $statusCode)
    {
        return $this->response = new PurchaseResponse($this, $data, $statusCode);
    }

    public function getHttpMethod()
    {
        return 'POST';
    }

    public function getAPI()
    {
        return 'PaymentIntents';
    }

    public function getData()
    {
        $data = [
            'amount' => intval($this->getParameter('amount')*100),
            'currency' => $this->getParameter('currency'),
            'collection_id' => $this->getParameter('collectionId'),
            'customer_name' => $this->getParameter('name'),
            'email' => $this->getParameter('email'),
            'payment_type' => $this->getParameter('payment_type'),
            'return_url' => $this->getParameter('returnUrl'),
            'callback_url' => $this->getParameter('notifyUrl'),
            'reject_url' => $this->getParameter('cancelUrl'),
        ];

        return [$data];
    }
}
