<?php

namespace Detail\Auth\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * @var Authorization\AuthorizationOptions
     */
    protected $authorization;

    /**
     * @var Identity\IdentityOptions
     */
    protected $identity;

    /**
     * @return Authorization\AuthorizationOptions|null
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
     * @return Identity\IdentityOptions|null
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
