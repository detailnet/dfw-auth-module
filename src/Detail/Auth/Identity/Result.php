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
     * @var IdentityInterface
     */
    protected $identity;

    /**
     * @var array
     */
    protected $messages;

    /**
     * @param boolean $valid
     * @param IdentityInterface $identity
     * @param array $messages
     */
    public function __construct($valid, IdentityInterface $identity = null, array $messages = array())
    {
        $this->valid = (bool) $valid;
        $this->identity = $identity;
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
     * @return IdentityInterface
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
