<?php

namespace Detail\Auth\Identity\Adapter;

use Detail\Auth\Identity\AdapterManager;
use Detail\Auth\Identity\Result;
use Detail\Auth\Identity\ResultInterface;

class ChainedAdapter extends BaseAdapter
{
    /**
     * @var AdapterManager
     */
    protected $adapters;

    /**
     * @var string[]
     */
    protected $adapterTypes = [];

    /**
     * @param AdapterManager $adapters
     * @param string[] $adapterTypes
     */
    public function __construct(AdapterManager $adapters, array $adapterTypes)
    {
        $this->setAdapters($adapters);
        $this->setAdapterTypes($adapterTypes);
    }

    /**
     * @return AdapterManager
     */
    public function getAdapters()
    {
        return $this->adapters;
    }

    /**
     * @param AdapterManager $adapters
     */
    public function setAdapters(AdapterManager $adapters)
    {
        $this->adapters = $adapters;
    }

    /**
     * @param string $type
     * @return AdapterInterface|null
     */
    public function getAdapter($type)
    {
        if (!$this->getAdapters()->hasAdapter($type)) {
            return null;
        }

        return $this->getAdapters()->getAdapter($type);
    }

    /**
     * @return string[]
     */
    public function getAdapterTypes()
    {
        return $this->adapterTypes;
    }

    /**
     * @param string[] $adapterTypes
     */
    public function setAdapterTypes(array $adapterTypes)
    {
        $this->adapterTypes = $adapterTypes;
    }

    /**
     * @return ResultInterface
     */
    protected function auth()
    {
        $messages = [];

        foreach ($this->getAdapterTypes() as $adapterType) {
            $adapter = $this->getAdapters()->getAdapter($adapterType);
            $result = $adapter->authenticate();

            if ($result->isValid()) {
                return $result;
            } else {
                $messages = array_merge($messages, $result->getMessages());
            }
        }

        return new Result(false, null, $messages);
    }
}
