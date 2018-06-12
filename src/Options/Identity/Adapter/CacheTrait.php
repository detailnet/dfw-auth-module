<?php

namespace Detail\Auth\Options\Identity\Adapter;

trait CacheTrait
{
    /**
     * @var string
     */
    protected $cache;

    /**
     * @return string
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param string $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }
}
