<?php

namespace Detail\Auth\Options\Identity\Adapter;

use Detail\Core\Options\AbstractOptions;

class AuthenticationAdapterAdapterOptions extends AbstractOptions
{
    use CacheTrait;
    use CredentialHeadersTrait;

    /**
     * @var string
     */
    protected $authenticationAdapter;

    /**
     * @return string
     */
    public function getAuthenticationAdapter()
    {
        return $this->authenticationAdapter;
    }

    /**
     * @param string $authenticationAdapter
     */
    public function setAuthenticationAdapter($authenticationAdapter)
    {
        $this->authenticationAdapter = $authenticationAdapter;
    }
}
