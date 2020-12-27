<?php
declare(strict_types=1);

namespace idimsh\PhpUnitTests\Unit;

class PHPUnitTestCase extends AbstractBaseUnitTestCase
{
    protected function setSelfDependency(
        $object,
        $propertyName = 'selfDependency',
        $propertyValue = null
    )
    {
        $this->setPropertyValue($object, $propertyName, $propertyValue ?? $this->selfDependency);
    }

    protected function unsetSelfDependency(
        $object,
        $propertyName = 'selfDependency'
    )
    {
        $this->setPropertyValue($object, $propertyName, $object);
    }
}
