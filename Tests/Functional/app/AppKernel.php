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

namespace Mmoreram\SimpleDoctrineMapping\Tests\Functional\app;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\TestBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class AppKernel
 */
class AppKernel extends Kernel
{
    /**
     * Register application bundles
     *
     * @return array Array of bundles instances
     */
    public function registerBundles()
    {
        $bundles = array(

            /**
             * Doctrine bundles
             */
            new FrameworkBundle(),
            new DoctrineBundle(),
            new TestBundle()

        );

        return $bundles;
    }

    /**
     * Register container configuration
     *
     * @param LoaderInterface $loader Loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $classInfo = new \ReflectionClass($this);
        $dir =  dirname($classInfo->getFileName());
        $loader->load($dir . '/config.yml');
    }

    /**
     * Return Cache dir
     *
     * @return string
     */
    public function getCacheDir()
    {
        return  sys_get_temp_dir() .
        DIRECTORY_SEPARATOR .
        'SimpleDoctrineMapping' .
        DIRECTORY_SEPARATOR .
        $this->getContainerClass() . '/Cache/';

    }

    /**
     * Return log dir
     *
     * @return string
     */
    public function getLogDir()
    {
        return  sys_get_temp_dir() .
        DIRECTORY_SEPARATOR .
        'SimpleDoctrineMapping' .
        DIRECTORY_SEPARATOR .
        $this->getContainerClass() . '/Log/';
    }
}
