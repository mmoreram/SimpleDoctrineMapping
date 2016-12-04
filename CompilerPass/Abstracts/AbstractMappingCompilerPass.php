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

namespace Mmoreram\SimpleDoctrineMapping\CompilerPass\Abstracts;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use Mmoreram\SimpleDoctrineMapping\DataObject\EntityMapping;
use Mmoreram\SimpleDoctrineMapping\Exception\ConfigurationInvalidException;
use Mmoreram\SimpleDoctrineMapping\Exception\EntityManagerNotFoundException;
use Mmoreram\SimpleDoctrineMapping\Exception\MappingExtensionWithoutDriverException;

/**
 * Class MappingCompilerPass.
 */
abstract class AbstractMappingCompilerPass implements CompilerPassInterface
{
    /**
     * Add mapping entity.
     *
     * This method adds a new Driver into global MappingDriverChain with single
     * entity mapping information.
     *
     * $entityManagerName must be an existing entityManager. By default doctrine
     * creates just one common EntityManager called default, but many can be
     * defined with different connection information
     *
     * p.e. default
     * p.e. anotherEntityManager
     *
     * $entityNamespace must be an existing namespace of Entity. This value also
     * can be a valid and existing container parameter, with an existing
     * namespace of Entity as value.
     *
     * p.e. MyBundle\Entity\User
     * p.e. mybundle.entity.user.class
     *
     * $entityMappingFilePath must be a path of an existing yml or xml file with
     * mapping information about $entityNamespace. This bundle uses Short Bundle
     * notation, with "@" symbol. This value also can be a valid and existing
     * container parameter, with a path of an existing yml or xml file as value.
     *
     * p.e. @MyBundle/Resources/config/doctrine/User.orm.yml
     * p.e. @MyBundle/Resources/config/doctrine/User.orm.xml
     * p.e. mybundle.entity.user.mapping_file_path
     *
     * Finally, $enable flag just allow you to add current mapping definition
     * into all Doctrine Map table, or just dismiss it. This is useful when you
     * want to give possibility to final user to enable or disable a mapping
     * class.
     *
     * @param ContainerBuilder $container             Container
     * @param string           $entityManagerName     EntityManager name
     * @param string           $entityNamespace       Entity namespace
     * @param string           $entityMappingFilePath Entity Mapping file path
     * @param bool|string      $enable                Entity mapping must be included
     *
     * @return AbstractMappingCompilerPass
     *
     * @throws EntityManagerNotFoundException Entity Manager nod found
     */
    protected function addEntityMapping(
        ContainerBuilder $container,
        $entityManagerName,
        $entityNamespace,
        $entityMappingFilePath,
        $enable = true
    ) : self {
        $entityMapping = $this->resolveEntityMapping(
            $container,
            $entityManagerName,
            $entityNamespace,
            $entityMappingFilePath,
            $enable
        );

        if ($entityMapping instanceof EntityMapping) {
            $this->registerLocatorConfigurator($container);
            $this->registerLocator($container, $entityMapping);
            $this->registerDriver($container, $entityMapping);
            $this->addDriverInDriverChain($container, $entityMapping);
            $this->addAliases($container, $entityMapping);
        }

        return $this;
    }

    /**
     * Resolve EntityMapping inputs and build a consistent EntityMapping object.
     *
     * This method returns null if the current entity has not been added
     *
     * @param ContainerBuilder $container
     * @param string           $entityManagerName
     * @param string           $entityNamespace
     * @param string           $entityMappingFilePath
     * @param string|bool      $enable
     *
     * @return EntityMapping|null
     *
     * @throws ConfigurationInvalidException  Configuration invalid
     * @throws EntityManagerNotFoundException Entity Manager not found
     */
    private function resolveEntityMapping(
        ContainerBuilder $container,
        string $entityManagerName,
        string $entityNamespace,
        string $entityMappingFilePath,
        $enable = true
    ) : ? EntityMapping {
        $enableEntityMapping = $this->resolveParameterName(
            $container,
            $enable
        );

        if (false === $enableEntityMapping) {
            return null;
        }

        $entityNamespace = $this->resolveParameterName($container, $entityNamespace);

        if (!class_exists($entityNamespace)) {
            throw new ConfigurationInvalidException('Entity ' . $entityNamespace . ' not found');
        }

        $entityMappingFilePath = $this->resolveParameterName($container, $entityMappingFilePath);
        $entityManagerName = $this->resolveParameterName($container, $entityManagerName);
        $this->resolveEntityManagerName($container, $entityManagerName);

        return new EntityMapping(
            $entityNamespace,
            $entityMappingFilePath,
            $entityManagerName
        );
    }

    /**
     * Register locator configurator.
     *
     * @param ContainerBuilder $container
     */
    private function registerLocatorConfigurator(ContainerBuilder $container)
    {
        $locatorConfiguratorId = 'simple_doctrine_mapping.locator_configurator';
        if ($container->hasDefinition($locatorConfiguratorId)) {
            return;
        }

        $locatorConfigurator = new Definition('Mmoreram\SimpleDoctrineMapping\Configurator\LocatorConfigurator');
        $locatorConfigurator->setPublic(true);
        $locatorConfigurator->setArguments([
            new Reference('kernel'),
        ]);

        $container->setDefinition($locatorConfiguratorId, $locatorConfigurator);
    }

    /**
     * Register the locator.
     *
     * @param ContainerBuilder $container
     * @param EntityMapping    $entityMapping
     */
    private function registerLocator(
        ContainerBuilder $container,
        EntityMapping $entityMapping
    ) {
        /**
         * Locator.
         */
        $locatorId = 'simple_doctrine_mapping.locator.' . $entityMapping->getUniqueIdentifier();
        $locator = new Definition('Mmoreram\SimpleDoctrineMapping\Locator\SimpleDoctrineMappingLocator');
        $locator->setPublic(false);
        $locator->setArguments([
            $entityMapping->getEntityNamespace(),
            [$entityMapping->getEntityMappingFilePath()],
        ]);
        $locator->setConfigurator([
            new Reference('simple_doctrine_mapping.locator_configurator'),
            'configure',
        ]);
        $container->setDefinition($locatorId, $locator);
    }

    /**
     * Register the driver.
     *
     * @param ContainerBuilder $container
     * @param EntityMapping    $entityMapping
     */
    private function registerDriver(
        ContainerBuilder $container,
        EntityMapping $entityMapping
    ) {
        /**
         * Specific extension Driver definition.
         */
        $mappingDriverId = 'doctrine.orm.' . $entityMapping->getUniqueIdentifier() . '_metadata_driver';
        $mappingDriverDefinition = new Definition($this->getDriverNamespaceGivenEntityMapping($entityMapping));
        $mappingDriverDefinition->setPublic(false);
        $mappingDriverDefinition->setArguments([
            new Reference('simple_doctrine_mapping.locator.' . $entityMapping->getUniqueIdentifier()),
        ]);
        $mappingDriverDefinition->addMethodCall('setGlobalBasename', [
            $entityMapping->getEntityNamespace(),
        ]);
        $container->setDefinition($mappingDriverId, $mappingDriverDefinition);
    }

    /**
     * Register and override the DriverChain definition.
     *
     * @param ContainerBuilder $container
     * @param EntityMapping    $entityMapping
     *
     * @throws EntityManagerNotFoundException Entity Manager nod found
     */
    private function addDriverInDriverChain(
        ContainerBuilder $container,
        EntityMapping $entityMapping
    ) {
        $chainDriverDefinition = $container
            ->getDefinition(
                'doctrine.orm.' . $entityMapping->getEntityManagerName() . '_metadata_driver'
            );

        $container->setParameter(
            'doctrine.orm.metadata.driver_chain.class',
            'Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain'
        );

        $chainDriverDefinition->addMethodCall('addDriver', [
            new Reference(
                'doctrine.orm.' . $entityMapping->getUniqueIdentifier() . '_metadata_driver'
            ),
            $entityMapping->getEntityNamespace(),
        ]);
    }

    /**
     * Add aliases for short Doctrine accessing mode.
     *
     * This method will make a vitual association between the bundle proposing
     * the entity and the entity namespace, even if both elements are in
     * different packages (Bundle + Component).
     *
     * Only useful and working when the relation between a bundle and the entity
     * folder path [[ Bundle (*) => (1) Entity path ]]
     *
     * @param ContainerBuilder $container
     * @param EntityMapping    $entityMapping
     */
    private function addAliases(
        ContainerBuilder $container,
        EntityMapping $entityMapping
    ) {
        $entityMappingFilePath = $entityMapping->getEntityMappingFilePath();
        if (strpos($entityMappingFilePath, '@') === 0) {
            $bundleName = trim(explode('/', $entityMappingFilePath, 2)[0]);
            $className = explode('\\', $entityMapping->getEntityNamespace());
            unset($className[count($className) - 1]);
            $configurationServiceDefinition = $container
                ->getDefinition(
                    'doctrine.orm.' . $entityMapping->getEntityManagerName() . '_configuration'
                );

            $configurationServiceDefinition->addMethodCall('addEntityNamespace', [
                $bundleName,
                implode('\\', $className),
            ]);
        }
    }

    /**
     * Resolvers.
     */

    /**
     * Return value of parameter name if exists
     * Return itself otherwise.
     *
     * @param ContainerBuilder $container
     * @param mixed            $parameterName
     *
     * @return mixed
     */
    private function resolveParameterName(
        ContainerBuilder $container,
        $parameterName
    ) {
        if (!is_string($parameterName)) {
            return $parameterName;
        }

        return $container->hasParameter($parameterName)
            ? $container->getParameter($parameterName)
            : $parameterName;
    }

    /**
     * Throws an exception if given entityName is not available or does
     * not exist.
     *
     * @param ContainerBuilder $container
     * @param string           $entityManagerName
     *
     * @throws EntityManagerNotFoundException Entity manager not found
     */
    private function resolveEntityManagerName(
        ContainerBuilder $container,
        string $entityManagerName
    ) {
        if (!$container->has('doctrine.orm.' . $entityManagerName . '_metadata_driver')) {
            throw new EntityManagerNotFoundException($entityManagerName);
        }
    }

    /**
     * Return the namespace of the driver to use given an EntityMapping.
     *
     * @param EntityMapping $entityMapping
     *
     * @return string
     *
     * @throws MappingExtensionWithoutDriverException Driver not found
     */
    private function getDriverNamespaceGivenEntityMapping(EntityMapping $entityMapping) : string
    {
        $namespace = 'Doctrine\ORM\Mapping\Driver\\';

        switch ($entityMapping->getExtension()) {
            case 'yml':
                $namespace .= 'YamlDriver';
                break;
            case 'yaml':
                $namespace .= 'YamlDriver';
                break;
            case 'xml':
                $namespace .= 'XmlDriver';
                break;
            default:
                throw new MappingExtensionWithoutDriverException($entityMapping->getExtension());
        }

        return $namespace;
    }
}
