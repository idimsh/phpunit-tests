<?php
declare(strict_types=1);

namespace idimsh\PhpUnitTests\Traits;

/**
 * Work on progress, not sure if they work or not.
 * The idea is to create stubs from the real classes and inject this trait in order to set/get private properties.
 */
trait PrivatePropertiesTrait
{
    use PropertiesAndMethodsReflectionTrait;

    /**
     * @param string $propertyName
     * @param  mixed $value
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public function __set(string $propertyName, $value)
    {
        $reflectionProperty = self::reflectionProperty($this, $propertyName);
        if (!$reflectionProperty) {
            throw self::reflectionPropertyException($this, $propertyName);
        }
        $reflectionProperty->setAccessible(true);
        if ($reflectionProperty->isStatic()) {
            $reflectionProperty->setValue(static::class, $value);
        }
        else {
            $reflectionProperty->setValue($this, $value);
        }
    }

    /**
     * @param string $propertyName
     * @return mixed
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public function __get(string $propertyName)
    {
        $reflectionProperty = self::reflectionProperty($this, $propertyName);
        if (!$reflectionProperty) {
            throw self::reflectionPropertyException($this, $propertyName);
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
