<?php

namespace Detail\Auth\Identity\Adapter;

use Zend\Authentication\AuthenticationService;

use Detail\Auth\Identity\Result;

class AuthenticationAdapter extends BaseAdapter
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
     * @return Result
     */
    protected function auth()
    {
        $authenticationService = $this->getAuthenticationService();
        $valid = true;
        $identity = $authenticationService->getIdentity();
        $messages = [];

        if ($identity === null) {
            $valid = false;
            $messages = ['User is not authenticated'];
        }

        return new Result($valid, $identity, $messages);
    }
}
