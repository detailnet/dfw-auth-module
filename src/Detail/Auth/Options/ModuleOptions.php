<?php

namespace Detail\Auth\Options;

use Detail\Core\Options\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * @var Authorization\AuthorizationOptions
     */
    protected $authorization;

    /**
     * @var array
     */
    protected $identity;

    /**
     * @return Authorization\AuthorizationOptions
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * @param array $authorization
     */
    public function setAuthorization(array $authorization)
    {
        $this->authorization = new Authorization\AuthorizationOptions($authorization);
    }

    /**
     * @return Identity\IdentityOptions
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param array $identity
     */
    public function setIdentity(array $identity)
    {
        $this->identity = new Identity\IdentityOptions($identity);
    }
}
