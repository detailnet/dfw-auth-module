<?php

namespace Detail\Auth\Options\Identity\Adapter;

trait CredentialHeadersTrait
{
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
