<?php

namespace Detail\Auth\Factory\Identity\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Detail\Auth\Identity\Exception;
use Detail\Auth\Identity\Controller\ThreeScaleController as Controller;

class ThreeScaleControllerFactory implements
    FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return Controller
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /** @var \Zend\Mvc\Controller\ControllerManager $controllerManager */

        $serviceLocator = $controllerManager->getServiceLocator();

        // Use same repository as the reporting listener
        /** @var \Detail\Auth\Identity\Listener\ThreeScaleReportingListener $reportingListener */
        $reportingListener = $serviceLocator->get('Detail\Auth\Identity\Listener\ThreeScaleReportingListener');
        $repository = $reportingListener->getRepository();

        // Use same 3scale client and service ID as the identity adapter
        /** @var \Detail\Auth\Identity\AdapterManager $adapters */
        $adapters = $serviceLocator->get('Detail\Auth\Identity\AdapterManager');
        /** @var \Detail\Auth\Identity\Adapter\ThreeScaleAdapter $adapter */
        $adapter = $adapters->getAdapter('Detail\Auth\Identity\Adapter\ThreeScaleAdapter');
        $client = $adapter->getClient();

        $controller = new Controller($repository, $client, $adapter->getServiceId());

        return $controller;
    }
}
