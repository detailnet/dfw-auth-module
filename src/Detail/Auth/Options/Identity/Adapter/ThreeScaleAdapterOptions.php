<?php

namespace Detail\Auth\Options\Identity\Adapter;

use Detail\Core\Options\AbstractOptions;

class ThreeScaleAdapterOptions extends AbstractOptions
{
    use CacheTrait;
    use CredentialHeadersTrait;

    /**
     * @var string
     */
    protected $client;

    /**
     * @var string
     */
    protected $cache;

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
