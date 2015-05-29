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

//    /**
//     * The adapter the result originated from.
//     *
//     * @var Adapter\AdapterInterface
//     */
//    protected $adapter;

    /**
     * @param boolean $valid
     * @param IdentityInterface $identity
     * @param array $messages
     */
    public function __construct(
        $valid,
        IdentityInterface $identity = null,
        array $messages = array()
//        Adapter\AdapterInterface $adapter = null
    ) {
        $this->valid = (bool) $valid;
        $this->identity = $identity;
        $this->messages = $messages;
//        $this->adapter = $adapter;
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

//    /**
//     * @return Adapter\AdapterInterface
//     */
//    public function getAdapter()
//    {
//        return $this->adapter;
//    }
}
