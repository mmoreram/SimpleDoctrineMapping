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

namespace Mmoreram\SimpleDoctrineMapping\CompilerPass\Abstracts;

use Mmoreram\SimpleDoctrineMapping\DataObject\EntityMapping;
use Mmoreram\SimpleDoctrineMapping\Exception\EntityManagerNotFound;
use Mmoreram\SimpleDoctrineMapping\Exception\MappingExtensionWithoutDriver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class MappingCompilerPass
 */
abstract class AbstractMappingCompilerPass implements CompilerPassInterface
{
    /**
     * Add mapping entity
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
     * $mappingFilePath must be a path of an existing yml or xml file, with
     * mapping information about $entityNamespace. This bundle uses Short Bundle
     * notation, with "@" symbol. This value also can be a valid and existing
     * container parameter, with a path of an existing yml or xml file as value.
     *
     * p.e. @MyBundle/Resources/config/doctrine/User.orm.yml
     * p.e. @MyBundle/Resources/config/doctrine/User.orm.xml
     * p.e. mybundle.entity.user.mapping_file_path
     *
     * @param ContainerBuilder $container             Container
     * @param string           $entityManagerName     EntityManager name
     * @param string           $entityNamespace       Entity namespace
     * @param string           $entityMappingFilePath Entity Mapping file path
     *
     * @return $this self Object
     *
     * @throws EntityManagerNotFound Entity Manager nod found
     */
    protected function addEntityMapping(
        ContainerBuilder $container,
        $entityManagerName,
        $entityNamespace,
        $entityMappingFilePath
    )
    {
        $entityMapping = $this->resolveEntityMapping(
            $container,
            $entityManagerName,
            $entityNamespace,
            $entityMappingFilePath
        );

        $this
            ->registerLocatorConfigurator($container)
            ->registerLocator($container, $entityMapping)
            ->registerDriver($container, $entityMapping)
            ->addDriverInDriverChain($container, $entityMapping);

        return $this;
    }

    /**
     * Resolve EntityMapping inputs and build a consistent EntityMapping object
     *
     * @param ContainerBuilder $container             Container
     * @param string           $entityManagerName     EntityManager name
     * @param string           $entityNamespace       Entity namespace
     * @param string           $entityMappingFilePath Entity Mapping file path
     *
     * @return AbstractMappingCompilerPass self Object
     *
     * @throws EntityManagerNotFound Entity Manager nod found
     */
    protected function resolveEntityMapping(
        ContainerBuilder $container,
        $entityManagerName,
        $entityNamespace,
        $entityMappingFilePath
    )
    {
        $entityNamespace = $this->resolveParameterName($container, $entityNamespace);
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
     * Register locator configurator
     *
     * @param ContainerBuilder $container Container
     *
     * @return AbstractMappingCompilerPass self Object
     */
    protected function registerLocatorConfigurator(ContainerBuilder $container)
    {
        $locatorConfiguratorId = 'simple_doctrine_mapping.locator_configurator';
        if ($container->hasDefinition($locatorConfiguratorId)) {
            return $this;
        }

        $locatorConfigurator = new Definition('Mmoreram\SimpleDoctrineMapping\Configurator\LocatorConfigurator');
        $locatorConfigurator->setPublic(true);
        $locatorConfigurator->setArguments([
            new Reference('kernel'),
        ]);

        $container->setDefinition($locatorConfiguratorId, $locatorConfigurator);

        return $this;
    }

    /**
     * Register the locator
     *
     * @param ContainerBuilder $container     Container
     * @param EntityMapping    $entityMapping Entity mapping
     *
     * @return AbstractMappingCompilerPass self Object
     */
    protected function registerLocator(
        ContainerBuilder $container,
        EntityMapping $entityMapping
    )
    {
        /**
         * Locator
         */
        $locatorId = 'simple_doctrine_mapping.locator.' . $entityMapping->getUniqueIdentifier();
        $locator = new Definition('Mmoreram\SimpleDoctrineMapping\Locator\SimpleDoctrineMappingLocator');
        $locator->setPublic(false);
        $locator->setArguments(array(
            [$entityMapping->getEntityMappingFilePath()]
        ));
        $locator->setConfigurator([
            new Reference('simple_doctrine_mapping.locator_configurator'),
            'configure'
        ]);
        $container->setDefinition($locatorId, $locator);

        return $this;
    }

    /**
     * Register the driver
     *
     * @param ContainerBuilder $container     Container
     * @param EntityMapping    $entityMapping Entity mapping
     *
     * @return AbstractMappingCompilerPass self Object
     */
    protected function registerDriver(
        ContainerBuilder $container,
        EntityMapping $entityMapping
    )
    {
        /**
         * Specific extension Driver definition
         */
        $mappingDriverId = 'doctrine.orm.' . $entityMapping->getUniqueIdentifier() . '_metadata_driver';
        $mappingDriverDefinition = new Definition($this->getDriverNamespaceGivenEntityMapping($entityMapping));
        $mappingDriverDefinition->setPublic(false);
        $mappingDriverDefinition->setArguments(array(
            new Reference('simple_doctrine_mapping.locator.' . $entityMapping->getUniqueIdentifier()),
        ));
        $mappingDriverDefinition->addMethodCall('setGlobalBasename', array(
            $entityMapping->getEntityNamespace(),
        ));
        $container->setDefinition($mappingDriverId, $mappingDriverDefinition);

        return $this;
    }

    /**
     * Register and override the DriverChain definition
     *
     * @param ContainerBuilder $container     Container
     * @param EntityMapping    $entityMapping Entity mapping
     *
     * @return AbstractMappingCompilerPass self Object
     *
     * @throws EntityManagerNotFound Entity Manager nod found
     */
    protected function addDriverInDriverChain(
        ContainerBuilder $container,
        EntityMapping $entityMapping
    )
    {
        $chainDriverDefinition = $container
            ->getDefinition(
                'doctrine.orm.' . $entityMapping->getEntityManagerName() . '_metadata_driver'
            );

        $container->setParameter(
            'doctrine.orm.metadata.driver_chain.class',
            'Mmoreram\SimpleDoctrineMapping\Mapping\MappingDriverChain'
        );

        $chainDriverDefinition->addMethodCall('addDriver', array(
            new Reference(
                'doctrine.orm.' . $entityMapping->getUniqueIdentifier() . '_metadata_driver'
            ),
        ));

        return $this;
    }

    /**
     * Resolvers
     */

    /**
     * Return value of parameter name if exists
     * Return itself otherwise
     *
     * @param ContainerBuilder $container     Container
     * @param string           $parameterName Parameter name
     *
     * @return string Parameter value
     */
    protected function resolveParameterName(ContainerBuilder $container, $parameterName)
    {
        return $container->hasParameter($parameterName)
            ? $container->getParameter($parameterName)
            : $parameterName;
    }

    /**
     * Throws an exception if given entityName is not available or does
     * not exist
     *
     * @param ContainerBuilder $container         Container
     * @param string           $entityManagerName EntityManager name
     *
     * @return string Parameter value
     *
     * @throws EntityManagerNotFound Entity manager not found
     */
    protected function resolveEntityManagerName(ContainerBuilder $container, $entityManagerName)
    {
        if (!$container->has('doctrine.orm.' . $entityManagerName . '_metadata_driver')) {

            throw new EntityManagerNotFound($entityManagerName);
        }

        return $this;
    }

    /**
     * Return the namespace of the driver to use given an EntityMapping
     *
     * @param EntityMapping $entityMapping Entity Mapping
     *
     * @return string Driver namespace
     *
     * @throws MappingExtensionWithoutDriver Driver not found
     */
    public function getDriverNamespaceGivenEntityMapping(EntityMapping $entityMapping)
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
                throw new MappingExtensionWithoutDriver($entityMapping->getExtension());
        }

        return $namespace;
    }
}
