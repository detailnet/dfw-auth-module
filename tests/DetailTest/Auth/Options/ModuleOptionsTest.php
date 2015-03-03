<?php

namespace DetailTest\Auth\Options;

class ModuleOptionsTest extends OptionsTestCase
{
    /**
     * @var \Detail\Auth\Options\ModuleOptions
     */
    protected $options;

    protected function setUp()
    {
        $this->options = $this->getOptions(
            'Detail\Auth\Options\ModuleOptions',
            array(
                'getAuthorization',
                'setAuthorization',
            )
        );
    }

    public function testOptionsExist()
    {
        $this->assertInstanceOf('Detail\Auth\Options\ModuleOptions', $this->options);
    }

    public function testAuthorizationCanBeSet()
    {
        $this->assertNull($this->options->getAuthorization());

        $this->options->setAuthorization(array());

        $authorization = $this->options->getAuthorization();

        $this->assertInstanceOf('Detail\Auth\Options\Authorization\AuthorizationOptions', $authorization);
    }
}
