<?php

namespace Detail\Auth\Options\Identity\Adapter;

use Detail\Core\Options\AbstractOptions;

class ThreeScaleAdapterOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $client;

    /**
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param string $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }
}
