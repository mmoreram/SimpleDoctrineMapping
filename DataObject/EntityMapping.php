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

namespace Mmoreram\SimpleDoctrineMapping\DataObject;

/**
 * Class EntityMapping.
 */
final class EntityMapping
{
    /**
     * @var string
     *
     * entityNamespace
     */
    private $entityNamespace;

    /**
     * @var string
     *
     * entityMappingFilePath
     */
    private $entityMappingFilePath;

    /**
     * @var string
     *
     * entityManagerName
     */
    private $entityManagerName;

    /**
     * @var string
     *
     * UniqueIdentifier
     */
    private $uniqueIdentifier;

    /**
     * @var string
     *
     * Extension
     */
    private $extension;

    /**
     * Constructor.
     *
     * @param string $entityNamespace
     * @param string $entityMappingFilePath
     * @param string $entityManagerName
     */
    public function __construct(
        string $entityNamespace,
        string $entityMappingFilePath,
        string $entityManagerName
    ) {
        $this->entityNamespace = $entityNamespace;
        $this->entityMappingFilePath = $entityMappingFilePath;
        $this->entityManagerName = $entityManagerName;
        $this->uniqueIdentifier = strtolower(str_replace('\\', '_', $entityNamespace));
        $this->extension = pathinfo($entityMappingFilePath, PATHINFO_EXTENSION);
    }

    /**
     * Get EntityManagerName.
     *
     * @return string
     */
    public function getEntityManagerName() : string
    {
        return $this->entityManagerName;
    }

    /**
     * Get EntityMappingFilePath.
     *
     * @return string
     */
    public function getEntityMappingFilePath() : string
    {
        return $this->entityMappingFilePath;
    }

    /**
     * Get EntityNamespace.
     *
     * @return string
     */
    public function getEntityNamespace() : string
    {
        return $this->entityNamespace;
    }

    /**
     * Get UniqueIdentifier.
     *
     * @return string
     */
    public function getUniqueIdentifier() : string
    {
        return $this->uniqueIdentifier;
    }

    /**
     * Get Extension.
     *
     * @return string
     */
    public function getExtension() : string
    {
        return $this->extension;
    }
}
