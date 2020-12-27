<?php
declare(strict_types=1);

namespace idimsh\PhpUnitTests\Traits;

trait PrivateMethodsTrait
{
    public function __call($method, array $args = [])
    {
        $parent = $this;
        if (!self::__methodExists($method, $parent)) {
            throw new \PHPUnit\Framework\MockObject\RuntimeException("method '$method' does not exist");
        }
        $reflectionMethod = new \ReflectionMethod($parent, $method);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invoke($parent, ...$args);
    }

    public static function __callStatic($method, array $args = [])
    {
        $parent = static::class;
        if (!self::__methodExists($method, $parent)) {
            throw new \PHPUnit\Framework\MockObject\RuntimeException("method '$method' does not exist");
        }
        $reflectionMethod = new \ReflectionMethod($parent, $method);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invoke(null, ...$args);
    }

    private static function __methodExists($method, &$parent) {
        do {
            $methodExists = method_exists($parent, $method);
            if (!$methodExists) {
                try {
                    new \ReflectionMethod($parent, $method);
                    $methodExists = true;
                }
                catch (\ReflectionException $e) {
                    // method not there.
                }
            }
            if ($methodExists) {
                break;
            }
            $parent = get_parent_class($parent);
        } while (!$methodExists && $parent);
        return $methodExists;
    }
}
