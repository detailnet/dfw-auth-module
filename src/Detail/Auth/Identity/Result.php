<?php

namespace Detail\Auth\Identity;

class Result implements
    ResultInterface
{
    /**
     * @var boolean
     */
    protected $valid;

    /**
     * @var array
     */
    protected $messages;

    /**
     * @param boolean $valid
     * @param array $messages
     */
    public function __construct($valid, array $messages = array())
    {
        $this->valid = (bool) $valid;
        $this->messages = $messages;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
