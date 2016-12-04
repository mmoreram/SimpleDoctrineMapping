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
use Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\SimpleTestBundle;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class MappingAliasTest.
 */
class MappingAliasTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel() : KernelInterface
    {
        return new BaseKernel([
            new SimpleTestBundle(),
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
     * Test entity name lookup.
     */
    public function testEntityNameLookup()
    {
        $class1 = new Class1(10, 'text1');
        $this->save($class1);

        $this->find(Class1::class, 1);
        $this->assertNotNull($this->find('@SimpleTestBundle:Class1', 10));
        $this->assertNull($this->find('@SimpleTestBundle:Class2', 1));
    }
}
