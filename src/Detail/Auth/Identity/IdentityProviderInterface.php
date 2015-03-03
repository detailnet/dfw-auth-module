<?php

namespace Detail\Auth\Identity;

interface IdentityProviderInterface
{
    /**
     * Get the identity.
     *
     * @return IdentityInterface|null
     */
    public function getIdentity();
}
