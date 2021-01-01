<?php
declare(strict_types=1);

namespace idimsh\PhpUnitTests\Traits;

trait SelfDependencyTestCaseTrait
{
    use UnitTestCaseTrait;

    /**
     * @param        $object
     * @param string $propertyName
     * @param null   $propertyValue
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    protected function setSelfDependency(
        $object,
        $propertyName = 'selfDependency',
        $propertyValue = null
    )
    {
        $this->setPropertyValue($object, $propertyName, $propertyValue ?? $this->selfDependency);
    }

    /**
     * @param        $object
     * @param string $propertyName
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    protected function unsetSelfDependency(
        $object,
        $propertyName = 'selfDependency'
    )
    {
        $this->setPropertyValue($object, $propertyName, $object);
    }
}
