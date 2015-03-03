<?php

namespace DetailTest\Auth;

use PHPUnit_Framework_TestCase as TestCase;

use Detail\Auth\Module;

class ModuleTest extends TestCase
{
    /**
     * @var Module
     */
    protected $module;

    protected function setUp()
    {
        $this->module = new Module();
    }

    public function testModuleProvidesAutoloaderConfig()
    {
        $config = $this->module->getAutoloaderConfig();

        $this->assertTrue(is_array($config));

        $this->assertArrayHasKey('Zend\Loader\StandardAutoloader', $config);
        $this->assertArrayHasKey('namespaces', $config['Zend\Loader\StandardAutoloader']);
        $this->assertArrayHasKey('Detail\Auth', $config['Zend\Loader\StandardAutoloader']['namespaces']);
    }

    public function testModuleProvidesConfig()
    {
        $config = $this->module->getConfig();

        $this->assertTrue(is_array($config));
        $this->assertArrayHasKey('detail_auth', $config);
        $this->assertTrue(is_array($config['detail_auth']));
//        $this->assertArrayHasKey('normalization', $config['detail_apigility']);
//        $this->assertTrue(is_array($config['detail_apigility']['normalization']));
//        $this->assertArrayHasKey('normalizer', $config['detail_apigility']['normalization']);
//        $this->assertEquals(
//            'Detail\Normalization\Normalizer\JMSSerializerBasedNormalizer',
//            $config['detail_apigility']['normalization']['normalizer']
//        );
    }

    public function testModuleProvidesControllerConfig()
    {
        $config = $this->module->getControllerConfig();

        $this->assertTrue(is_array($config));
    }

    public function testModuleProvidesServiceConfig()
    {
        $config = $this->module->getServiceConfig();

        $this->assertTrue(is_array($config));
    }
}
