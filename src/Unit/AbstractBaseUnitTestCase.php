<?php
declare(strict_types=1);

namespace idimsh\PhpUnitTests\Unit;

use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\RuntimeException;

/**
 * @deprecated
 */
abstract class AbstractBaseUnitTestCase extends \PHPUnit\Framework\TestCase
{
    protected function createMockWithProtectedMethods(string $originalClassName): MockObject
    {
        return $this->getMockBuilderWithProtectedMethods($originalClassName)->getMock();
    }

    protected function createMockWithProtectedMethodsForAbstractClass(string $originalClassName): MockObject
    {
        return $this->getMockBuilderWithProtectedMethods($originalClassName)->getMockForAbstractClass();
    }

    protected function getMockBuilderWithProtectedMethods(string $originalClassName): MockBuilder
    {
        $return = $this->getMockBuilder($originalClassName)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes();

        try {
            $reflectionClass = new \ReflectionClass($originalClassName);
        }
        catch (\ReflectionException $e) {
            throw new RuntimeException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
        $methods = [];
        foreach (
            $reflectionClass->getMethods(
                \ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED | \ReflectionMethod::IS_PRIVATE
            ) as $method
        ) {
            $methods[] = $method->getName();
        }
        if (method_exists($return, 'onlyMethods')) {
            // new in v8
            $return->onlyMethods($methods);
        }
        else {
            // will be deprecated in v9 and removed in v10
            $return->setMethods($methods);
        }
        return $return;
    }

    /**
     * Invoke a method (mostly private or protected) against $object with parameters passed
     * additional to this method
     *
     * @param        $object
     * @param string $methodName
     * @return mixed
     */
    protected function invokeMethod($object, string $methodName)
    {
        try {
            $reflectionMethod = new \ReflectionMethod($object, $methodName);
        }
        catch (\ReflectionException $e) {
            throw new RuntimeException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
        $reflectionMethod->setAccessible(true);
        $args = func_get_args();
        array_shift($args);
        array_shift($args);
        if ($args) {
            return $reflectionMethod->invoke($object, ...$args);
        }
        else {
            return $reflectionMethod->invoke($object);
        }
    }

    /**
     * Invoke a method (mostly private or protected) against $object with parameters that can
     * be passed by reference (some or all).
     *
     * @param        $object
     * @param string $methodName
     * @param array  $params like: [
     *                       0 => &$param0ByRef,
     *                       1 => $param1ByValue,
     *                       ]
     * @return mixed
     */
    protected function invokeMethodParamsByReference($object, string $methodName, array &$params)
    {
        $reflectionMethod = new \ReflectionMethod($object, $methodName);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invokeArgs($object, $params);
    }

    protected function setPropertyValue($classNameOrObject, string $propertyName, $value): void
    {
        $reflectionProperty = new \ReflectionProperty($classNameOrObject, $propertyName);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($classNameOrObject, $value);
    }

    protected function getPropertyValue($classNameOrObject, string $propertyName)
    {
        $reflectionProperty = new \ReflectionProperty($classNameOrObject, $propertyName);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->getValue($classNameOrObject);
    }
}
