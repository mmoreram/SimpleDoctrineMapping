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

namespace Mmoreram\SimpleDoctrineMapping\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class LoadBundleTest
 */
class LoadBundleTest extends WebTestCase
{
    /**
     * Set up
     */
    public function testBundle()
    {
        gc_collect_cycles();

        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->setAutoExit(false);
        $container = $kernel->getContainer();
        $container
            ->get('doctrine.orm.default_entity_manager')
            ->getClassMetadata('Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\Entity\Class1');

        $container
            ->get('doctrine.orm.alternative_entity_manager')
            ->getClassMetadata('Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\Entity\Class2');

        $container
            ->get('doctrine.orm.default_entity_manager')
            ->getClassMetadata('Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\MyOtherEntityNamespace\Class3');

        $container
            ->get('doctrine.orm.alternative_entity_manager')
            ->getClassMetadata('Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\MyOtherEntityNamespace\Class4');
    }

    /**
     * Attempts to guess the kernel location.
     *
     * When the Kernel is located, the file is required.
     *
     * @return string The Kernel class name
     *
     * @throws \RuntimeException
     */
    protected static function getKernelClass()
    {
        return 'Mmoreram\SimpleDoctrineMapping\Tests\Functional\app\AppKernel';
    }

    /**
     * Creates a Kernel.
     *
     * Available options:
     *
     *  * environment
     *  * debug
     *
     * @param array $options An array of options
     *
     * @return KernelInterface A KernelInterface instance
     */
    protected static function createKernel(array $options = [])
    {
        $kernelClass = self::getKernelClass();

        return new $kernelClass('test', true);
    }
}
