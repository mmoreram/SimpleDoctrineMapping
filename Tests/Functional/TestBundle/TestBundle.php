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

namespace Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle;

use Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\CompilerPass\MappingCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class TestBundle
 */
class TestBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MappingCompilerPass());
    }
}
