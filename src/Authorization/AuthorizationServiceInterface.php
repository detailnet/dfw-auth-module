<?php

namespace Detail\Auth\Authorization;

interface AuthorizationServiceInterface
{
    /**
     * @param string $action
     * @param mixed $context
     * @return boolean
     */
    public function isAllowed($action, $context = null);
}
