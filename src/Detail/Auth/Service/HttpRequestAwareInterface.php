<?php

namespace Detail\Auth\Service;

use Zend\Http\Request as HttpRequest;

interface HttpRequestAwareInterface
{
    /**
     * @param HttpRequest $request
     */
    public function setRequest(HttpRequest $request);
}