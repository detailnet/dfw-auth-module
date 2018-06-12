<?php

namespace Detail\Auth\Identity\Exception;

use Detail\Auth\Exception as BaseException;

class InvalidArgumentException extends BaseException\InvalidArgumentException implements
    ExceptionInterface
{
}
