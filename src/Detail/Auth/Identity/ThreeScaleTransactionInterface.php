<?php

namespace Detail\Auth\Identity;

use DateTime;

interface ThreeScaleTransactionInterface
{
    /**
     * @return string
     */
    public function getAppId();

    /**
     * @return DateTime
     */
    public function getReceivedOn();

    /**
     * @return array
     */
    public function getUsage();

    /**
     * @return string
     */
    public function getRequest();

    /**
     * @return string
     */
    public function getRequestForReporting();

    /**
     * @return string
     */
    public function getResponse();

    /**
     * @return string
     */
    public function getResponseForReporting();

    /**
     * @return int
     */
    public function getResponseCode();

    /**
     * @return int
     */
    public function estimateSize();

    /**
     * @param int $threshold
     * @return int
     */
    public function prepareForReporting($threshold);
}
