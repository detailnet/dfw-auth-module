<?php

namespace Detail\Auth\Identity;

class ThreeScaleResult extends Result
{
    /**
     * @var array|null
     */
    protected $usage;

    /**
     * @param boolean $valid
     * @param IdentityInterface $identity
     * @param array $messages
     * @param array|null $usage
     */
    public function __construct(
        $valid,
        IdentityInterface $identity = null,
        array $messages = array(),
//        Adapter\AdapterInterface $adapter = null,
        $usage = null
    ) {
        parent::__construct($valid, $identity, $messages);

        $this->usage = $usage;
    }

    /**
     * @return array
     */
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * @return boolean
     */
    public function hasUsage()
    {
        return is_array($this->getUsage()) && !empty($this->getUsage());
    }
}
