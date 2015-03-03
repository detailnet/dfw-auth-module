<?php

namespace Detail\Auth\Identity\Adapter;

use Zend\Authentication\AuthenticationService;

use Detail\Auth\Identity\Result;
use Detail\Auth\Identity\ResultInterface;

class AuthenticationAdapter implements
    AdapterInterface
{
    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @param AuthenticationService $authenticationService
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->setAuthenticationService($authenticationService);
    }

    /**
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        return $this->authenticationService;
    }

    /**
     * @param AuthenticationService $authenticationService
     */
    public function setAuthenticationService(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * @return ResultInterface
     */
    public function authenticate()
    {
        /** @todo Replace with real implementation */
        return new Result(false, array('Not yet implemented'));
    }
}
