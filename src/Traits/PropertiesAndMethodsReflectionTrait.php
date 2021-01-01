<?php
declare(strict_types=1);

namespace idimsh\PhpUnitTests\Traits;

use PHPUnit\Framework\MockObject\RuntimeException;

trait PropertiesAndMethodsReflectionTrait
{
    protected static function reflectionMethod($objectOrClassName, string $methodName): ?\ReflectionMethod
    {
        $subjectClass     = is_object($objectOrClassName) ? get_class($objectOrClassName) : $objectOrClassName;
        $reflectionMethod = null;
        do {
            try {
                $reflectionMethod = new \ReflectionMethod($subjectClass, $methodName);
            }
            catch (\ReflectionException $e) {
                $reflectionMethod = null;
            }
            if ($reflectionMethod) {
                break;
            }
            $subjectClass = get_parent_class($subjectClass);
        } while (!$reflectionMethod && $subjectClass);
        return $reflectionMethod;
    }

    protected static function reflectionProperty($objectOrClassName, string $propertyName): ?\ReflectionProperty
    {
        $subjectClass       = is_object($objectOrClassName) ? get_class($objectOrClassName) : $objectOrClassName;
        $reflectionProperty = null;
        do {
            try {
                $reflectionProperty = new \ReflectionProperty($subjectClass, $propertyName);
            }
            catch (\ReflectionException $e) {
                $reflectionProperty = null;
            }
            if ($reflectionProperty) {
                break;
            }
            $subjectClass = get_parent_class($subjectClass);
        } while (!$reflectionProperty && $subjectClass);
        return $reflectionProperty;
    }

    protected static function reflectionMethodException($objectOrClassName, string $methodName): RuntimeException
    {
        $subjectClass = is_object($objectOrClassName) ? get_class($objectOrClassName) : $objectOrClassName;
        return new RuntimeException(
            sprintf(
                'Method [%s] was not found to be part of class: [%s] or any of its parents',
                $methodName,
                $subjectClass
            )
        );
    }

    protected static function reflectionPropertyException($objectOrClassName, string $propertyName): RuntimeException
    {
        $subjectClass = is_object($objectOrClassName) ? get_class($objectOrClassName) : $objectOrClassName;
        return new RuntimeException(
            sprintf(
                'Property [%s] was not found to be part of class: [%s] or any of its parents',
                $propertyName,
                $subjectClass
            )
        );
    }
}
