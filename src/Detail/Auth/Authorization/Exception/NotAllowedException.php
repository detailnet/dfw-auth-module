<?php

namespace Detail\Auth\Authorization\Exception;

use ZfcRbac\Exception\UnauthorizedExceptionInterface;

use Detail\Auth\Exception\DomainException;

class NotAllowedException extends DomainException implements
    ExceptionInterface,
    UnauthorizedExceptionInterface
{
}
