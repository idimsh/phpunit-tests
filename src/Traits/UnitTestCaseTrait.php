<?php
declare(strict_types=1);

namespace idimsh\PhpUnitTests\Traits;

use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\RuntimeException;

trait UnitTestCaseTrait
{
    use PropertiesAndMethodsReflectionTrait;

    /**
     * @param string $originalClassName
     * @return MockObject
     * @throws RuntimeException
     */
    protected function createMockWithProtectedMethods(string $originalClassName): MockObject
    {
        return $this->getMockBuilderWithProtectedMethods($originalClassName)->getMock();
    }

    /**
     * @param string $originalClassName
     * @return MockObject
     * @throws RuntimeException
     * @throws \PHPUnit\Framework\Exception
     */
    protected function createMockWithProtectedMethodsForAbstractClass(string $originalClassName): MockObject
    {
        return $this->getMockBuilderWithProtectedMethods($originalClassName)->getMockForAbstractClass();
    }

    /**
     * @param string $originalClassName
     * @return MockBuilder
     * @throws RuntimeException
     */
    protected function getMockBuilderWithProtectedMethods(string $originalClassName): MockBuilder
    {
        /** @var \PHPUnit\Framework\TestCase $this */
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
     * @throws RuntimeException
     */
    protected function invokeMethod($object, string $methodName)
    {
        $reflectionMethod = self::reflectionMethod($object, $methodName);
        if (!$reflectionMethod) {
            throw self::reflectionMethodException($object, $methodName);
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
     * @throws RuntimeException
     */
    protected function invokeMethodParamsByReference($object, string $methodName, array &$params)
    {
        $reflectionMethod = self::reflectionMethod($object, $methodName);
        if (!$reflectionMethod) {
            throw self::reflectionMethodException($object, $methodName);
        }
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invokeArgs($object, $params);
    }

    /**
     * @param        $classNameOrObject
     * @param string $propertyName
     * @param        $value
     * @throws RuntimeException
     */
    protected function setPropertyValue($classNameOrObject, string $propertyName, $value): void
    {
        $reflectionProperty = self::reflectionProperty($classNameOrObject, $propertyName);
        if (!$reflectionProperty) {
            throw self::reflectionPropertyException($classNameOrObject, $propertyName);
        }
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($classNameOrObject, $value);
    }

    /**
     * @param        $classNameOrObject
     * @param string $propertyName
     * @throws RuntimeException
     */
    protected function getPropertyValue($classNameOrObject, string $propertyName)
    {
        $reflectionProperty = self::reflectionProperty($classNameOrObject, $propertyName);
        if (!$reflectionProperty) {
            throw self::reflectionPropertyException($classNameOrObject, $propertyName);
        }
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->getValue($classNameOrObject);
    }
}
