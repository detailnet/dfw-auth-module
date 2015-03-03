<?php

namespace Detail\Auth\Authorization;

interface AuthorizationServiceAwareInterface
{
    public function setAuthorizationService(AuthorizationServiceInterface $authorizationService);
}
