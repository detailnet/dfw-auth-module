<?php

namespace Detail\Auth\Identity;

interface ResultInterface
{
    /**
     * @return boolean
     */
    public function isValid();

    /**
     * @return array
     */
    public function getMessages();
}
