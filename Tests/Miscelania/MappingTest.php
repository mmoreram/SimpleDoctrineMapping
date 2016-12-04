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
use Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\Entity\Class1;
use Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\Entity\Class2;
use Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\MyOtherEntityNamespace\Class3;
use Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\MyOtherEntityNamespace\Class4;
use Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\TestBundle;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class MappingTest.
 */
class MappingTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel() : KernelInterface
    {
        return new BaseKernel([
            new TestBundle(),
        ], [
            'imports' => [
                ['resource' => '@BaseBundle/Resources/config/providers.yml'],
                ['resource' => '@BaseBundle/Resources/test/doctrine.test.yml'],
            ],
            'doctrine' => [
                'orm' => [
                    'entity_managers' => [
                        'default' => [
                            'connection' => 'default',
                        ],
                        'alternative' => [
                            'connection' => 'default',
                        ],
                    ],
                ],
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
     * Test all mapping data.
     */
    public function testMappingData()
    {
        $this->assertSame(
            $this->getObjectManager(Class1::class),
            $this->get('doctrine.orm.default_entity_manager')
        );

        $class1 = new Class1(1, 'text1');
        $this->save($class1);

        $this->assertSame(
            $this->getObjectManager(Class2::class),
            $this->get('doctrine.orm.alternative_entity_manager')
        );

        $class2 = new Class2(2, 'text2');
        $this->save($class2);

        $this->assertSame(
            $this->getObjectManager(Class3::class),
            $this->get('doctrine.orm.default_entity_manager')
        );

        $class3 = new Class3(3, 'text3');
        $this->save($class3);

        $this->assertSame(
            $this->getObjectManager(Class4::class),
            $this->get('doctrine.orm.alternative_entity_manager')
        );

        $class4 = new Class4(4, 'text4');
        $this->save($class4);
    }
}
