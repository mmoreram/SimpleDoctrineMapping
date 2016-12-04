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

namespace Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Mmoreram\SimpleDoctrineMapping\CompilerPass\Abstracts\AbstractMappingCompilerPass;

/**
 * Class SimpleMappingCompilerPass.
 */
class SimpleMappingCompilerPass extends AbstractMappingCompilerPass
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
                'Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\Entity\Class1',
                '@SimpleTestBundle/Mapping/Class1.orm.yml'
            )
            ->addEntityMapping(
                $container,
                'alternative',
                'Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\Entity\Class2',
                '@SimpleTestBundle/Mapping/Class2.custom.orm.yml',
                true
            );
    }
}
