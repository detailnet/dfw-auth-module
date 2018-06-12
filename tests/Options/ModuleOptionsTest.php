<?php

namespace DetailTest\Auth\Options;

use Detail\Auth\Options\Authorization\AuthorizationOptions;
use Detail\Auth\Options\ModuleOptions;

class ModuleOptionsTest extends OptionsTestCase
{
    /**
     * @var ModuleOptions
     */
    protected $options;

    protected function setUp()
    {
        $this->options = $this->getOptions(
            ModuleOptions::CLASS,
            [
                'getAuthorization',
                'setAuthorization',
            ]
        );
    }

    public function testOptionsExist()
    {
        $this->assertInstanceOf(ModuleOptions::CLASS, $this->options);
    }

    public function testAuthorizationCanBeSet()
    {
        $this->assertNull($this->options->getAuthorization());

        $this->options->setAuthorization([]);

        $authorization = $this->options->getAuthorization();

        $this->assertInstanceOf(AuthorizationOptions::CLASS, $authorization);
    }
}
