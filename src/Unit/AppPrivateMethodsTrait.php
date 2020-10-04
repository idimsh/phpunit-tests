<?php
declare(strict_types=1);
/*
* This file is part of idimsh\PhpUnitTests\Unit;
*
* Author: Abdulrahman Dimashki <idimsh@gmail.com>
*/

namespace idimsh\PhpUnitTests\Unit;

trait AppPrivateMethodsTrait
{
    public function __call($method, array $args = [])
    {
        $parent = $this;
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
        } while (!$methodExists);
        if (!$methodExists) {
            throw new \BadMethodCallException("method '$method' does not exist");
        }
        $reflectionMethod = new \ReflectionMethod($parent, $method);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invoke($parent, ...$args);
    }

    public static function __callStatic($method, array $args = [])
    {
        $parent = static::class;
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
        } while (!$methodExists);
        if (!$methodExists) {
            throw new \BadMethodCallException("method '$method' does not exist");
        }
        $reflectionMethod = new \ReflectionMethod($parent, $method);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invoke(null, ...$args);
    }
}
