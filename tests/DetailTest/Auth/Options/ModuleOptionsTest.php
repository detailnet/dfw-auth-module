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
//                'getNormalization',
//                'setNormalization',
            )
        );
    }

    public function testOptionsExist()
    {
        $this->assertInstanceOf('Detail\Auth\Options\ModuleOptions', $this->options);
    }

//    public function testNormalizationCanBeSet()
//    {
//        $this->assertNull($this->options->getNormalization());
//
//        $this->options->setNormalization(array());
//
//        $normalization = $this->options->getNormalization();
//
//        $this->assertInstanceOf('Detail\Auth\Options\Normalization\NormalizationOptions', $normalization);
//    }
}
