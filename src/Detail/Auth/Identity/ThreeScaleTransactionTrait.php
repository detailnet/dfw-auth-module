<?php

namespace Detail\Auth\Identity;

use DateTime;

trait ThreeScaleTransactionTrait
{
    /**
     * @var string
     */
    protected $appId;

    /**
     * @var DateTime
     */
    protected $receivedOn;

    /**
     * @var array
     */
    protected $usage;

    /**
     * @var string
     */
    protected $request;

    /**
     * @var string
     */
    protected $requestForReporting;

    /**
     * @var string
     */
    protected $response;

    /**
     * @var string
     */
    protected $responseForReporting;

    /**
     * @var integer
     */
    protected $responseCode;

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @return DateTime
     */
    public function getReceivedOn()
    {
        return $this->receivedOn;
    }

    /**
     * @return array
     */
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getRequestForReporting()
    {
        return $this->requestForReporting ?: $this->getRequest();
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getResponseForReporting()
    {
        return $this->responseForReporting ?: $this->getResponse();
    }

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @return int
     */
    public function estimateSize()
    {
        // Match TheeScaleClient's internal structure for a transaction
        $transaction = array(
            'app_id' => $this->getAppId(),
            'timestamp' => $this->getReceivedOn()->format('Y-m-d H:i:s P'),
            'usage' => $this->getUsage(),
            'log' => array(
                'request' => urlencode($this->getRequestForReporting()),
                'response' => urlencode($this->getResponseForReporting()),
                'code' => $this->getResponseCode(),
            ),
        );

        $body = http_build_query($transaction, '', '&');

        return strlen($body);
    }

    /**
     * @param int $threshold
     * @return int
     */
    public function prepareForReporting($threshold = null)
    {
        $estimatedSize = $this->estimateSize();

        // Nothing to do if size is already below threshold
        if ($threshold === null || $estimatedSize <= $threshold) {
            return $estimatedSize;
        }

        $placeholder = '...';

        $sizeToSave = $estimatedSize - $threshold;
        $sizeToSaveLeft = $sizeToSave;

        // Only response and request can be truncated (in this order)
        $truncatableProperties = array(
            'response' => 'getResponse',
            'request'  => 'getRequest',
        );

        foreach ($truncatableProperties as $property => $accessor) {
            $propertyValue = $this->$accessor();
            $propertySize = strlen($propertyValue);

            $sizeToTrim = $sizeToSaveLeft + strlen($placeholder);

            $propertyForReporting = $property . 'ForReporting';
            $propertyValueForReporting = substr($propertyValue, 0, -$sizeToTrim) . $placeholder;
            $propertySizeForReporting = strlen($propertyValueForReporting);

            $this->$propertyForReporting = $propertyValueForReporting;

            $sizeToSaveLeft = $sizeToSaveLeft - $propertySize + $propertySizeForReporting;

            if ($sizeToSaveLeft <= 0) {
                return $this->estimateSize();
            }
        }

        // If we end up here it means we probably couldn't truncate to the given threshold
        $estimatedSizeForReporting = $this->estimateSize();

        if ($estimatedSizeForReporting > $threshold) {
            throw new Exception\RuntimeException(
                sprintf(
                    'Failed to truncate transaction by %d to %d chars; it is still %d chars long',
                    $sizeToSave,
                    $threshold,
                    $estimatedSizeForReporting
                )
            );
        }

        return $estimatedSizeForReporting;
    }
}
