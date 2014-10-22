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

namespace Mmoreram\SimpleDoctrineMapping\Mapping;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\FileDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\MappingException;

/**
 * Class MappingDriverChain
 */
class MappingDriverChain implements MappingDriver
{
    /**
     * The default driver.
     *
     * @var MappingDriver|null
     */
    private $defaultDriver = null;

    /**
     * @var array
     */
    private $drivers = array();

    /**
     * Gets the default driver.
     *
     * @return MappingDriver|null
     */
    public function getDefaultDriver()
    {
        return $this->defaultDriver;
    }

    /**
     * Set the default driver.
     *
     * @param MappingDriver $driver
     *
     * @return void
     */
    public function setDefaultDriver(MappingDriver $driver)
    {
        $this->defaultDriver = $driver;
    }

    /**
     * Adds a nested driver.
     *
     * @param MappingDriver $nestedDriver
     *
     * @return void
     */
    public function addDriver(MappingDriver $nestedDriver)
    {
        $this->drivers[] = $nestedDriver;
    }

    /**
     * Gets the array of nested drivers.
     *
     * @return array $drivers
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * Loads the metadata for the specified class into the provided container.
     *
     * @param string        $className
     * @param ClassMetadata $metadata
     *
     * @return void
     *
     * @throws MappingException Class not found
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        /**
         * @var $driver FileDriver
         */
        foreach ($this->drivers as $driver) {

            $namespace = $driver->getGlobalBasename();

            if ($this->classNameIsAllowed($className, $namespace)) {
                $driver->loadMetadataForClass($className, $metadata);

                return;
            }
        }

        if (null !== $this->getDefaultDriver()) {
            $this->getDefaultDriver()->loadMetadataForClass($className, $metadata);

            return;
        }

        throw MappingException::classNotFoundInNamespaces(
            $className,
            array_keys($this->getDrivers())
        );
    }

    /**
     * Gets the names of all mapped classes known to this driver.
     *
     * @return array The names of all mapped classes known to this driver.
     */
    public function getAllClassNames()
    {
        $classNames = array();
        $driverClasses = array();

        /**
         * @var $driver FileDriver
         */
        foreach ($this->drivers as $driver) {

            $namespace = $driver->getGlobalBasename();
            $oid = spl_object_hash($driver);

            if (!isset($driverClasses[$oid])) {
                $driverClasses[$oid] = $driver->getAllClassNames();
            }

            foreach ($driverClasses[$oid] as $className) {
                if ($this->classNameIsAllowed($className, $namespace)) {
                    $classNames[$className] = true;
                }
            }
        }

        if (null !== $this->defaultDriver) {
            foreach ($this->defaultDriver->getAllClassNames() as $className) {
                $classNames[$className] = true;
            }
        }

        return array_keys($classNames);
    }

    /**
     * Returns whether the class with the specified name should have its metadata loaded.
     * This is only the case if it is either mapped as an Entity or a MappedSuperclass.
     *
     * @param string $className
     *
     * @return boolean
     */
    public function isTransient($className)
    {
        /**
         * @var $driver FileDriver
         */
        foreach ($this->drivers as $driver) {
            $namespace = $driver->getGlobalBasename();
            if ($this->classNameIsAllowed($className, $namespace)) {
                return $driver->isTransient($className);
            }
        }

        if ($this->defaultDriver !== null) {
            return $this->defaultDriver->isTransient($className);
        }

        return true;
    }

    /**
     * Class is allowed to be parsed in current namespace
     *
     * @param string $className Class name
     * @param string $namespace Namespace
     *
     * @return bool class is allowed to be parsed
     */
    protected function classNameIsAllowed($className, $namespace)
    {
        return $className === $namespace;
    }
}
