<?php

namespace Detail\Auth\Identity\Model;

use ZfcRbac\Identity\IdentityInterface as RbacIdentityInterface;

use Detail\Auth\Identity\IdentityInterface;

class ApiIdentity implements
    IdentityInterface,
    RbacIdentityInterface
{
    /**
     * @var string
     */
    protected $appId;

    /**
     * @var string
     */
    protected $appKey;

    /**
     * @var array
     */
    protected $roles = array();

    /**
     * @param string $appId
     * @param string $appKey
     * @param array $roles
     */
    public function __construct(
        $appId,
        $appKey,
        array $roles
    ) {
        $this->setAppId($appId);
        $this->setAppKey($appKey);
        $this->setRoles($roles);
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return string
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * @param string $appKey
     */
    public function setAppKey($appKey)
    {
        $this->appKey = $appKey;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }
}
