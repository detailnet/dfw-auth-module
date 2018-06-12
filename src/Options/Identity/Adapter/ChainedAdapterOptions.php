<?php

namespace Detail\Auth\Options\Identity\Adapter;

class ChainedAdapterOptions extends AdapterOptions
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
