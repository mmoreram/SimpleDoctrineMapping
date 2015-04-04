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

namespace Mmoreram\SimpleDoctrineMapping\DataObject;

/**
 * Class EntityMapping
 */
class EntityMapping
{
    /**
     * @var string
     *
     * entityNamespace
     */
    protected $entityNamespace;

    /**
     * @var string
     *
     * entityMappingFilePath
     */
    protected $entityMappingFilePath;

    /**
     * @var string
     *
     * entityManagerName
     */
    protected $entityManagerName;

    /**
     * @var string
     *
     * UniqueIdentifier
     */
    protected $uniqueIdentifier;

    /**
     * @var string
     *
     * Extension
     */
    protected $extension;

    /**
     * @param string $entityNamespace       Entity namespace
     * @param string $entityMappingFilePath Entity mapping file path
     * @param string $entityManagerName     Entity manager name
     */
    public function __construct(
        $entityNamespace,
        $entityMappingFilePath,
        $entityManagerName
    ) {
        $this->entityNamespace = $entityNamespace;
        $this->entityMappingFilePath = $entityMappingFilePath;
        $this->entityManagerName = $entityManagerName;
        $this->uniqueIdentifier = strtolower(str_replace('\\', '_', $entityNamespace));
        $this->extension = pathinfo($entityMappingFilePath, PATHINFO_EXTENSION);
    }

    /**
     * Get EntityManagerName
     *
     * @return mixed EntityManagerName
     */
    public function getEntityManagerName()
    {
        return $this->entityManagerName;
    }

    /**
     * Get EntityMappingFilePath
     *
     * @return mixed EntityMappingFilePath
     */
    public function getEntityMappingFilePath()
    {
        return $this->entityMappingFilePath;
    }

    /**
     * Get EntityNamespace
     *
     * @return mixed EntityNamespace
     */
    public function getEntityNamespace()
    {
        return $this->entityNamespace;
    }

    /**
     * Get UniqueIdentifier
     *
     * @return string UniqueIdentifier
     */
    public function getUniqueIdentifier()
    {
        return $this->uniqueIdentifier;
    }

    /**
     * Get Extension
     *
     * @return string Extension
     */
    public function getExtension()
    {
        return $this->extension;
    }
}
