<?php

namespace Detail\Auth\Service;

use Zend\Http\Request as HttpRequest;

trait HttpRequestAwareTrait
{
    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @return HttpRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param HttpRequest $request
     */
    public function setRequest(HttpRequest $request)
    {
        $this->request = $request;
    }
}