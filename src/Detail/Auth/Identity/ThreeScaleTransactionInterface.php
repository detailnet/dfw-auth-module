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
    public function getResponse();

    /**
     * @return int
     */
    public function getResponseCode();
}
