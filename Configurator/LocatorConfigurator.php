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

namespace Mmoreram\SimpleDoctrineMapping\Configurator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\SimpleDoctrineMapping\Exception\ConfigurationInvalidException;
use Mmoreram\SimpleDoctrineMapping\Locator\SimpleDoctrineMappingLocator;

/**
 * Class LocatorConfigurator
 */
class LocatorConfigurator
{
    /**
     * @var KernelInterface
     *
     * Kernel
     */
    protected $kernel;

    /**
     * Construct method
     *
     * @param KernelInterface $kernel Kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * If there is a parameter named like the driver locator path, replace it
     * with the value of such parameter.
     *
     * @param SimpleDoctrineMappingLocator $locator Locator
     *
     * @throws ConfigurationInvalidException Configuration invalid
     */
    public function configure(SimpleDoctrineMappingLocator $locator)
    {
        $path = $locator->getPaths()[0];

        $resourcePathExploded = explode('/', $path);
        $resourcePathRoot = array_shift($resourcePathExploded);

        if (strpos($resourcePathRoot, '@') === 0) {
            $mappingFileBundle = ltrim($resourcePathRoot, '@');
            $bundle = $this->kernel->getBundle($mappingFileBundle);

            if ($bundle instanceof BundleInterface) {
                $resourcePathRoot = $bundle->getPath();
            }
        }

        array_unshift($resourcePathExploded, $resourcePathRoot);

        $path = implode('/', $resourcePathExploded);

        if (!file_exists($path)) {
            throw new ConfigurationInvalidException('Mapping file "' . $path . '" does not exist');
        }

        $locator->setPaths([$path]);
    }
}
