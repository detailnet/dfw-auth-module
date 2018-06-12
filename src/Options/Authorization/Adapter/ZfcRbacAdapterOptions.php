<?php

namespace Detail\Auth\Options\Authorization\Adapter;

use Zend\Stdlib\AbstractOptions;

class ZfcRbacAdapterOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $service;

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }
}
