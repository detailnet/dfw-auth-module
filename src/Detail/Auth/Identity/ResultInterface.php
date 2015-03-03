<?php

namespace Detail\Auth\Identity;

interface ResultInterface
{
    /**
     * @return boolean
     */
    public function isValid();

    /**
     * @return IdentityInterface
     */
    public function getIdentity();

    /**
     * @return array
     */
    public function getMessages();
}
