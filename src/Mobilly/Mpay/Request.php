<?php

namespace Mobilly\Mpay;

/**
 * Request object.
 * @package Mobilly\Mpay
 */
class Request
{
    const F_SERVICE_ID = 'service_id';
    const F_AMOUNT = 'amount';
    const F_CURRENCY = 'currency';
    const F_SUMMARY = 'summary';
    const F_RETURN_URL = 'return_url';
    const F_RESULT_URL = 'result_url';
    const F_FIRST_NAME = 'firstname';
    const F_LAST_NAME = 'lastname';
    const F_EMAIL = 'email';
    const F_USER = 'user';
    const F_TIMESTAMP = 'timestamp';
    const F_SIGNATURE = 'signature';
    const F_POST_PROCESSOR = 'post_processor';
    const F_POST_PROCESS_DATA = 'post_process_data';
    const F_LANGUAGE = 'language';

    const DEFAULT_CURRENCY = 'EUR';

    private $data = [];

    /**
     * @var SecurityContext
     */
    private $context;


    public function __construct(SecurityContext $context)
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function get()
    {
        $signer = $this->context->getSigner();

        $this->data[self::F_TIMESTAMP] = date('c');
        $this->data[self::F_USER] = $this->context->getUser();
        $this->data[self::F_SIGNATURE] = $signer->sign($this->data);

        return $this->data;
    }

    /**
     * @return string
     */
    public function getJson()
    {
        return json_encode($this->get());
    }

    /**
     * Set Mobilly service ID (provided by Mobilly).
     *
     * @param $serviceId
     * @return $this
     * @throws RequestException
     */
    public function setServiceId($serviceId)
    {
        if ( ! is_integer($serviceId)) {
            throw new RequestException(sprintf('Service id should be integer "%s" given.', $serviceId));
        }

        $this->data[self::F_SERVICE_ID] = $serviceId;

        return $this;
    }

    /**
     * Set amount of transaction.
     *
     * @param integer $amount Amount in minor currency fraction, e.g. 300 in case of 3 EUR
     * @param string $currency Currency in form of ISO4217
     *
     * @return $this
     * @throws RequestException
     */
    public function setAmount($amount, $currency = self::DEFAULT_CURRENCY)
    {
        if ( ! is_integer($amount)) {
            throw new RequestException(sprintf('Amount should be integer "%s" given.', $amount));
        }
        if ( ! is_string($currency) || 3 != strlen($currency)) {
            throw new RequestException(sprintf('Currency should comply with ISO4217 "%s" given.', $currency));
        }

        $this->data[self::F_AMOUNT] = $amount;
        $this->data[self::F_CURRENCY] = $currency;

        return $this;
    }


    /**
     * Set descriptive summary.
     *
     * @param string $summary
     * @return $this
     */
    public function setSummary($summary)
    {
        $this->data[self::F_SUMMARY] = $summary;

        return $this;
    }

    /**
     * Set return URL.
     *
     * @param string $url URL where browser should be redirected after transaction processing.
     * @return $this
     */
    public function setReturnUrl($url)
    {
        $this->data[self::F_RETURN_URL] = $url;

        return $this;
    }

    /**
     * Set result URL.
     *
     * @param string $url URL where Mpay will send transaction result.
     * @return $this
     */
    public function setResultUrl($url)
    {
        $this->data[self::F_RESULT_URL] = $url;

        return $this;
    }

    /**
     * Set payer contact info.
     *
     * @param string $firstName First name.
     * @param string $lastName Last name.
     * @param string $email E-mail address.
     *
     * @return $this
     */
    public function setContacts($firstName, $lastName, $email)
    {
        $this->data[self::F_FIRST_NAME] = $firstName;
        $this->data[self::F_LAST_NAME] = $lastName;
        $this->data[self::F_EMAIL] = $email;

        return $this;
    }

    /**
     * Set post processor.
     *
     * For more details about post processor usage please contact dev@mobilly.lv     *
     *
     * @param string $postProcessor Post processor name.
     * @param string $postProcessData Post process data passed to post processor when executed.
     *
     * @return $this
     */
    public function setPostProcessor($postProcessor, $postProcessData)
    {
        $this->data[self::F_POST_PROCESSOR] = $postProcessor;
        $this->data[self::F_POST_PROCESS_DATA] = $postProcessData;

        return $this;
    }

    /**
     * Set language for Mpay UI.
     *
     * @param string $language ISO 639-1
     */
    public function setLanguage($language)
    {
        $this->data[self::F_LANGUAGE] = $language;
    }
}