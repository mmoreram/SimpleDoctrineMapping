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

namespace Mmoreram\SimpleDoctrineMapping\Locator;

use Doctrine\Common\Persistence\Mapping\Driver\DefaultFileLocator;
use Doctrine\ORM\Mapping\MappingException;

/**
 * Class SimpleDoctrineMappingLocator
 */
class SimpleDoctrineMappingLocator extends DefaultFileLocator
{
    /**
     * Entity namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * Array with mapping file path
     *
     * @var array
     */
    protected static $pathsMap = [];

    /**
     * Set paths
     *
     * @param array $paths Paths
     *
     * @return SimpleDoctrineMappingLocator self Object
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
        self::$pathsMap[$this->namespace] = $this->paths;

        return $this;
    }

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
     * {@inheritDoc}
     */
    public function fileExists($className)
    {
        return (isset($this->paths[0]) && is_file($this->paths[0]));
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames($globalBasename = null)
    {
        return $globalBasename;
    }

    /**
     * {@inheritDoc}
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
