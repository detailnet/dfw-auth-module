<?php

namespace Detail\Auth\Options\Identity\Adapter;

use Zend\Stdlib\AbstractOptions;

class AuthenticationAdapterOptions extends AbstractOptions
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
