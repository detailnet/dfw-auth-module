<?php

namespace Detail\Auth\Options;

use Detail\Core\Options\AbstractOptions;

class ThreeScaleOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $providerKey;

    /**
     * @var string
     */
    protected $serviceId;

    /**
     * @var ThreeScaleReportingOptions
     */
    protected $reporting;

    /**
     * @return string
     */
    public function getProviderKey()
    {
        return $this->providerKey;
    }

    /**
     * @param string $providerKey
     */
    public function setProviderKey($providerKey)
    {
        $this->providerKey = $providerKey;
    }

    /**
     * @return string
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @param string $serviceId
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;
    }

    /**
     * @return ThreeScaleReportingOptions
     */
    public function getReporting()
    {
        if ($this->reporting === null) {
            $this->reporting = new ThreeScaleReportingOptions();
        }

        return $this->reporting;
    }

    /**
     * @param array $reporting
     */
    public function setReporting(array $reporting)
    {
        $this->getReporting()->setFromArray($reporting);
    }
}
