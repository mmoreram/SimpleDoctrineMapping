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

namespace Mmoreram\SimpleDoctrineMapping\Locator;

use Doctrine\Common\Persistence\Mapping\Driver\DefaultFileLocator;
use Doctrine\ORM\Mapping\MappingException;

/**
 * Class SimpleDoctrineMappingLocator.
 */
final class SimpleDoctrineMappingLocator extends DefaultFileLocator
{
    /**
     * Entity namespace.
     *
     * @var string
     */
    private $namespace;

    /**
     * Array with mapping file path.
     *
     * @var array
     */
    private static $pathsMap = [];

    /**
     * Constructor.
     *
     * @param string $namespace
     * @param array  $prefixes
     */
    public function __construct($namespace, array $prefixes)
    {
        $this->namespace = $namespace;
        $this->paths = $prefixes;
    }

    /**
     * Set paths.
     *
     * @param array $paths
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
        self::$pathsMap[$this->namespace] = $this->paths;
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists($className)
    {
        return isset($this->paths[0]) && is_file($this->paths[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllClassNames($globalBasename = null)
    {
        return $globalBasename;
    }

    /**
     * {@inheritdoc}
     */
    public function findMappingFile($className)
    {
        if (!$this->fileExists($className)) {
            throw MappingException::mappingFileNotFound($className, $this->paths[0]);
        }

        if (isset(self::$pathsMap[$className])) {
            $this->paths = self::$pathsMap[$className];
        }

        return $this->paths[0];
    }
}
