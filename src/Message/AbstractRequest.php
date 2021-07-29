<?php

namespace Omnipay\PayEx\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use GuzzleHttp\Middleware;

/**
 * Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $domain = [
        'live' => 'api.payex.io',
        'test' => 'sandbox-payexapi.azurewebsites.net'
    ];

    protected function getDomain()
    {
        return $this->getTestMode() ? $this->domain['test'] : $this->domain['live'];
    }

    protected function getEndpoint()
    {

        return 'https://'. $this->getDomain() . '/api/v1/'. $this->getAPI();
    }

    public function sendData($data)
    {
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Content-type' => 'application/json',
            'curl.options' => [CURLOPT_SSLVERSION => 6],
            'http_errors' => false
        ];

        try {
            // Guzzle HTTP Client createRequest does funny things when a GET request
            // has attached data, so don't send the data if the method is GET.
            if ($this->getHttpMethod() == 'GET') {
                $httpResponse = $this->httpClient->request(
                    $this->getHttpMethod(),
                    $this->getEndpoint() . '?' . http_build_query($data),
                    $headers
                );
            } else {
                $httpResponse = $this->httpClient->request(
                    $this->getHttpMethod(),
                    $this->getEndpoint(),
                    $headers,
                    $this->toJSON($data)
                );
            }

            $body = $httpResponse->getBody(true);
            $jsonToArrayResponse = !empty($body) ? json_decode($body, true) : array();
            return $this->response = $this->createResponse($jsonToArrayResponse, $httpResponse->getStatusCode());
        } catch (\Exception $e) {
            throw new InvalidResponseException('Error communicating with payment gateway: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function getToken()
    {
        $token = $this->getParameter('token');
        if (!empty($token) && strtotime($token['expiration']) > time()) {
            return $token['token'];
        }

        try {
            $httpResponse = $this->httpClient->request(
                'POST',
                'https://'. $this->getDomain() . '/api/Auth/Token',
                [
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . $this->getAuthToken(),
                    'Content-type' => 'application/json',
                    'curl.options' => [CURLOPT_SSLVERSION => 6],
                    'http_errors' => false
                ],
                ''
            );
        } catch (\Exception $e) {
            throw new InvalidResponseException('Error communicating with payment gateway: ' . $e->getMessage(), $e->getCode());
        }

        $body = $httpResponse->getBody(true);
        if (empty($body)) {
            throw new InvalidResponseException('Error communicating with payment gateway: Failed to get authorization token');
        }

        $responseBody = json_decode($body, true);
        $this->setToken($responseBody);

        return $this->getToken();
    }

    public function toJSON($data, $options = 0)
    {
        // Because of PHP Version 5.3, we cannot use JSON_UNESCAPED_SLASHES option
        // Instead we would use the str_replace command for now.
        // TODO: Replace this code with return json_encode($this->toArray(), $options | 64); once we support PHP >= 5.4
        if (version_compare(phpversion(), '5.4.0', '>=') === true) {
            return json_encode($data, $options | 64);
        }
        return str_replace('\\/', '/', json_encode($data, $options));
    }

    public function getAuthToken()
    {
        return base64_encode($this->getUsername() .':'. $this->getSecret());
    }

    public function setToken($value)
    {
        return $this->setParameter('token', $value);
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

    public function getName()
    {
        return $this->getParameter('name');
    }

    public function setName($value)
    {
        return $this->setParameter('name', $value);
    }

    public function getEmail()
    {
        return $this->getParameter('email');
    }

    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    public function getCollectionId()
    {
        return $this->getParameter('collectionId');
    }

    public function setCollectionId($value)
    {
        return $this->setParameter('collectionId', $value);
    }

    public function getPaymentType()
    {
        return $this->getParameter('payment_type');
    }

    public function setPaymentType($value)
    {
        return $this->setParameter('payment_type', $value);
    }

    public function getTxnId()
    {
        return $this->getParameter('txn_id');
    }

    public function setTxnId($value)
    {
        return $this->setParameter('txn_id', $value);
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
