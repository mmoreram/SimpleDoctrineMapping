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

namespace Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Class TestExtension
 */
class TestExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $container->setParameter(
            'testbundle.class3.class',
            'Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\MyOtherEntityNamespace\Class3'
        );

        $container->setParameter(
            'testbundle.class3.mapping_file',
            '@TestBundle/Mapping/Class3.orm.yaml'
        );

        $container->setParameter(
            'testbundle.class3.enable',
            true
        );

        $container->setParameter(
            'testbundle.class4.class',
            'Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\MyOtherEntityNamespace\Class4'
        );

        $container->setParameter(
            'testbundle.class4.mapping_file',
            '@TestBundle/Mapping/Class4.orm.xml'
        );

        $container->setParameter(
            'testbundle.class4.entity_manager',
            'alternative'
        );
    }
}
