<?php

namespace Detail\Auth\Identity;

class ThreeScaleResult extends Result
{
    /**
     * @var array|null
     */
    protected $usage;

    /**
     * @var string|null
     */
    protected $appId;

    /**
     * @var string|null
     */
    protected $appKey;

    /**
     * @param boolean $valid
     * @param IdentityInterface $identity
     * @param array $messages
     * @param array|null $usage
     * @param string|null $appId
     * @param string|null $appKey
     */
    public function __construct(
        $valid,
        IdentityInterface $identity = null,
        array $messages = array(),
//        Adapter\AdapterInterface $adapter = null,
        $usage = null,
        $appId = null,
        $appKey = null
    ) {
        parent::__construct($valid, $identity, $messages);

        $this->usage = $usage;
        $this->appId = $appId;
        $this->appKey = $appKey;
    }

    /**
     * @return array|null
     */
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * @return boolean
     */
    public function hasUsage()
    {
        return is_array($this->getUsage()) && !empty($this->getUsage());
    }

    /**
     * @return string|null
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @return string|null
     */
    public function getAppKey()
    {
        return $this->appKey;
    }
}
