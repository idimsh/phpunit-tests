<?php
declare(strict_types=1);

namespace idimsh\PhpUnitTests\Traits;
use PHPUnit\Framework\MockObject\RuntimeException;

/**
 * Work on progress, not sure if they work or not.
 * The idea is to create stubs from the real classes and inject this trait in order to set/get private properties.
 */
trait PrivatePropertiesTrait
{
    public function __set(string $propertyName, $value)
    {
        try {
            $reflectionProperty = new \ReflectionProperty(get_parent_class($this), $propertyName);
        }
        catch (\ReflectionException $e) {
            throw new RuntimeException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
        $reflectionProperty->setAccessible(true);
        if ($reflectionProperty->isStatic()) {
            $reflectionProperty->setValue(static::class, $value);
        }
        else {
            $reflectionProperty->setValue($this, $value);
        }
    }


    public function __get(string $propertyName)
    {
        try {
            $reflectionProperty = new \ReflectionProperty(get_parent_class($this), $propertyName);
        }
        catch (\ReflectionException $e) {
            throw new RuntimeException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
        $reflectionProperty->setAccessible(true);
        if ($reflectionProperty->isStatic()) {
            return $reflectionProperty->getValue(static::class);
        }
        else {
            return $reflectionProperty->getValue($this);
        }
    }
}
