<?php

namespace Detail\Auth\Identity\Adapter;

use Detail\Auth\Identity\ResultInterface;

interface AdapterInterface
{
    /**
     * @return ResultInterface
     */
    public function authenticate();
}
