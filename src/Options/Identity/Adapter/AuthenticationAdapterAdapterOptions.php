<?php

namespace Detail\Auth\Options\Identity\Adapter;

class AuthenticationAdapterAdapterOptions extends AdapterOptions
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
