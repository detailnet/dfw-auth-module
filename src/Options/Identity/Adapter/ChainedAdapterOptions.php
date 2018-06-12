<?php

namespace Detail\Auth\Options\Identity\Adapter;

use Zend\Stdlib\AbstractOptions;

class ChainedAdapterOptions extends AbstractOptions
{
    /**
     * @var array
     */
    protected $adapters = [];

    /**
     * @return array
     */
    public function getAdapters()
    {
        return $this->adapters;
    }

    /**
     * @param array $adapters
     */
    public function setAdapters(array $adapters)
    {
        $this->adapters = $adapters;
    }
}
