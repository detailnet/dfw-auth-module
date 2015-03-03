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
}
