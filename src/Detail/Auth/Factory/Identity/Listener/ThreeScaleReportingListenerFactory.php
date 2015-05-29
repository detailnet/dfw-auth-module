<?php

namespace Detail\Auth\Factory\Identity\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Identity\Listener\ThreeScaleReportingListener as Listener;

class ThreeScaleReportingListenerFactory implements
    FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return Listener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
//        /** @var \Detail\Auth\Options\ModuleOptions $moduleOptions */
//        $moduleOptions = $serviceLocator->get('Detail\Auth\Options\ModuleOptions');

        return new Listener();
    }
}
