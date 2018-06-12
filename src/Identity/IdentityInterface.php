<?php

namespace Detail\Auth\Identity;

interface IdentityInterface
{
    /**
     * Get the list of roles of this identity.
     *
     * @return string[]
     */
    public function getRoles();
}
