<?php
declare(strict_types=1);

namespace idimsh\PhpUnitTests\Traits;

trait PrivateMethodsTrait
{
    use PropertiesAndMethodsReflectionTrait;

    /**
     * @param string $method
     * @param array  $args
     * @return mixed
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public function __call($method, array $args = [])
    {
        $reflectionMethod = self::reflectionMethod($this, $method);
        if (!$reflectionMethod) {
            throw self::reflectionMethodException($this, $method);
        }
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invoke($this, ...$args);
    }

    /**
     * @param  string $method
     * @param array   $args
     * @return mixed
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public static function __callStatic($method, array $args = [])
    {
        $reflectionMethod = self::reflectionMethod(static::class, $method);
        if (!$reflectionMethod) {
            throw self::reflectionMethodException(static::class, $method);
        }
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invoke(null, ...$args);
    }
}
