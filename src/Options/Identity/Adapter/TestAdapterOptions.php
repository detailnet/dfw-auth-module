<?php

namespace Detail\Auth\Options\Identity\Adapter;

class TestAdapterOptions extends AdapterOptions
{
    /**
     * @var boolean
     */
    protected $result;

    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @return boolean
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param boolean $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }
}
