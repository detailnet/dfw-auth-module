<?php

namespace Detail\Auth\Options;

use Detail\Core\Options\AbstractOptions;

class ThreeScaleReportingOptions extends AbstractOptions
{
    /**
     * @var bool
     */
    protected $disabled = false;

    /**
     * @var string
     */
    protected $repository;

    /**
     * @return string
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param string $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    public function isEnabled()
    {
        return !$this->isDisabled();
    }

    /**
     * @param boolean $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (bool) $disabled;
    }
}
