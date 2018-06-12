<?php

namespace DetailTest\Auth;

use PHPUnit\Framework\TestCase;

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

    public function testModuleProvidesConfig(): void
    {
        $config = $this->module->getConfig();

        $this->assertTrue(is_array($config));
        $this->assertArrayHasKey('detail_auth', $config);
        $this->assertTrue(is_array($config['detail_auth']));
    }
}
