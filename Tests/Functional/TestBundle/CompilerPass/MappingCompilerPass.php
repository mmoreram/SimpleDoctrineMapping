<?php

/**
 * SimpleDoctrineMapping for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\CompilerPass;

use Mmoreram\SimpleDoctrineMapping\CompilerPass\Abstracts\AbstractMappingCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class MappingCompilerPass
 */
class MappingCompilerPass extends AbstractMappingCompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $this
            ->addEntityMapping(
                $container,
                'default',
                'Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\Entity\Class1',
                '@TestBundle/Mapping/Class1.orm.yml'
            )
            ->addEntityMapping(
                $container,
                'alternative',
                'Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\Entity\Class2',
                '@TestBundle/Mapping/Class2.custom.orm.yml'
            )
            ->addEntityMapping(
                $container,
                'default',
                'testbundle.class3.class',
                'testbundle.class3.mapping_file'
            )
            ->addEntityMapping(
                $container,
                'testbundle.class4.entity_manager',
                'testbundle.class4.class',
                'testbundle.class4.mapping_file'
            )
        ;
    }
}
