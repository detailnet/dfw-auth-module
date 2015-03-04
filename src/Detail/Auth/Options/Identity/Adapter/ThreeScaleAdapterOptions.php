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
     * @var string
     */
    protected $appIdHeader;

    /**
     * @var string
     */
    protected $appKeyHeader;

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

    /**
     * @return string
     */
    public function getAppIdHeader()
    {
        return $this->appIdHeader;
    }

    /**
     * @param string $appIdHeader
     */
    public function setAppIdHeader($appIdHeader)
    {
        $this->appIdHeader = $appIdHeader;
    }

    /**
     * @return string
     */
    public function getAppKeyHeader()
    {
        return $this->appKeyHeader;
    }

    /**
     * @param string $appKeyHeader
     */
    public function setAppKeyHeader($appKeyHeader)
    {
        $this->appKeyHeader = $appKeyHeader;
    }
}
