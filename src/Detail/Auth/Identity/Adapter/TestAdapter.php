<?php

namespace Detail\Auth\Identity\Adapter;

use Detail\Auth\Identity\Result;

class TestAdapter extends BaseAdapter
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
     * @param boolean $result
     * @param string $errorMessage
     */
    public function __construct($result, $errorMessage = null)
    {
        $this->setResult($result);

        if ($errorMessage !== null) {
            $this->setErrorMessage($errorMessage);
        }
    }

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

    /**
     * @return Result
     */
    protected function auth()
    {
        return new Result($this->getResult(), null, array($this->getErrorMessage()));
    }
}
