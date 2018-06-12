<?php

namespace Detail\Auth\Identity;

class AuthenticationResult extends Result
{
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
     * @param string|null $appId
     * @param string|null $appKey
     */
    public function __construct(
        $valid,
        IdentityInterface $identity = null,
        array $messages = [],
        $appId = null,
        $appKey = null
    ) {
        parent::__construct($valid, $identity, $messages);

        $this->appId = $appId;
        $this->appKey = $appKey;
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
