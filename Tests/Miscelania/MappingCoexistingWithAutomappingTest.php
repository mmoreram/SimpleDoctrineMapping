<?php

/*
 * SimpleDoctrineMapping for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Mmoreram\SimpleDoctrineMapping\Tests\Miscelania;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;
use Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\ExtendedTestBundle;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class MappingCoexistingWithAutomappingTest.
 */
class MappingCoexistingWithAutomappingTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel() : KernelInterface
    {
        return new BaseKernel([
            new ExtendedTestBundle(),
        ], [
            'imports' => [
                ['resource' => '@BaseBundle/Resources/config/providers.yml'],
                ['resource' => '@BaseBundle/Resources/test/framework.test.yml'],
                ['resource' => '@BaseBundle/Resources/test/doctrine.test.yml'],
            ],
            'doctrine' => [
                'orm' => [
                    'auto_mapping' => true,
                ],
            ],
            'parameters' => [
                'testbundle.class3.class' => 'Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\MyOtherEntityNamespace\Class3',
                'testbundle.class3.mapping_file' => '@@ExtendedTestBundle/Mapping/Class3.orm.yaml',
                'testbundle.class3.enable' => true,
            ],
        ]);
    }

    /**
     * Schema must be loaded in all test cases.
     *
     * @return bool
     */
    protected static function loadSchema() : bool
    {
        return true;
    }

    /**
     * Testing bundle built properly.
     */
    public function testBundle()
    {
        $this->assertNull($this->find('Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\Entity\Class1', 1));
        $this->assertNull($this->find('Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\Entity\Class2', 1));
        $this->assertNull($this->find('Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\MyOtherEntityNamespace\Class3', 1));
    }
}
