<?php

namespace Detail\Auth\Identity;

use ZfcRbac\Identity\IdentityInterface as ZfcRbacIdentityInterface;

class Identity implements
    IdentityInterface,
    ZfcRbacIdentityInterface
{
    /**
     * @var string
     */
    protected $role;

    /**
     * @param string $role
     */
    public function __construct($role)
    {
        $this->role = $role;
    }

    /**
     * Get the list of roles of this identity.
     *
     * @return string[]
     */
    public function getRoles()
    {
        return array($this->role);
    }
}
