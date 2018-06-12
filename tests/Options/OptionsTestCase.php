<?php

namespace DetailTest\Auth\Options;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class OptionsTestCase extends TestCase
{
    protected function getOptions(string $class, array $methods): MockObject
    {
        $mockedMethods = array_diff($this->getMethods($class), $methods);

        return $this->getMockBuilder($class)->setMethods($mockedMethods)->getMock();
    }

    private function getMethods(string $class, bool $autoload = true): array
    {
        $methods = [];

        if (class_exists($class, $autoload) || interface_exists($class, $autoload)) {
            $reflector = new \ReflectionClass($class);

            foreach ($reflector->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_ABSTRACT) as $method) {
                $methods[] = $method->getName();
            }
        }

        return $methods;
    }
}
