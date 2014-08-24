Simple Doctrine Mapping for Symfony2
=====

*KISS*, Remember?

Este bundle agiliza y reduce la complejidad del automapping de Doctrine. La
primera premisa para utilizar este bundle es desactivar, si está activado, el
`auto_mapping` de los managers de doctrine. Una vez desactivados, manos a la
obra, vamos a definir cada una de nuestra entidades como se mapea con el ORM.

El bundle ofrece única y exclusivamente un CompilerPass abstracto, para que
cada proyecto pueda extenderlo y utilizar un único método. Repito,
*Keep it simple*, muy importante.

CompilerPass
------------

Un CompilerPass, para la gente que no conozca lo que es, no es otra cosa que una
clase inyectada al Container y ejecutada justo antes de ser compilado. Digamos
que es la última oportunidad de hacer cambios en el mapa de Inyección de
Dependencias antes que este se construya y pase a ser de solo lectura.

Este bundle propone una forma bastante sencilla de definir como se mapean las
entidades de tu bundle, dejando al propio bundle la capacidad para definir
las localizaciones de los mismos y el EntityManager al que irá asociado.

``` php
<?php

/**
 * SimpleDoctrineMapping for Symfony2
 */

namespace TestBundle\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Mmoreram\SimpleDoctrineMapping\CompilerPass\Abstracts\AbstractMappingCompilerPass;

/**
 * Class MappingCompilerPass
 */
class MappingCompilerPass extends AbstractMappingCompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $this
            ->addEntityMapping(
                $container,
                'default',
                'TestBundle\Entity\User',
                '@TestBundle/Mapping/User.orm.yml'
            )
        ;
    }
}
```

And that's it. Con esto estamos añadiendo a nuestro mapa de Doctrine nuestra
entidad sin ningún tipo de magia oscura y de forma completamente explícita.

addEntityMapping()
------------------

El método *addEntityMapping()* nos ofrece pocas opciones, pero las necesarias
para poder definir el mapa de entidades en la gran mayoría de los casos.

``` php
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
```

Todos los valores son obligatorios.

Parameters
----------

Imaginemos ahora que nuestro bundle se instala como Vendor. Evidentemente no
podemos asegurar que nuestras entidades van a ser representadas por una
clase y un fichero de mapping específico ya que en este caso, perderíamos toda
opción de hacer overriding. Por esta razón este bundle permite trabajar con
parámetros de container de forma completamente transparente.

``` yml
parameters:

    #
    # Classes
    #
    test_bundle.entity.user.class: "TestBundle\Entity\User"
    test_bundle.entity.user.mapping_file_path: "@TestBundle/Mapping/Class.orm.yml"
    test_bundle.entity.user.entity_manager: default
```

En este caso, nuestro bundle pondrá a merced del proyecto el poder sobreescribir
la configuración por defecto.

Finalmente podemos definir el mapping de nuestra entidad utilizando simplemente
los parámetros.

``` php
<?php

/**
 * SimpleDoctrineMapping for Symfony2
 */

namespace TestBundle\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Mmoreram\SimpleDoctrineMapping\CompilerPass\Abstracts\AbstractMappingCompilerPass;

/**
 * Class MappingCompilerPass
 */
class MappingCompilerPass extends AbstractMappingCompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $this
            ->addEntityMapping(
                $container,
                'test_bundle.entity.user.entity_manager',
                'test_bundle.entity.user.class',
                'test_bundle.entity.user.mapping_file_path'
            )
        ;
    }
}
```

Ahora, cualquier proyecto que desee sobrecargar cualquiera de estos datos solo
deberá sobreescribir el valor de tales parámetros.

Tags
----

* Use last unstable version ( alias of `dev-master` ) to stay always in last commit
* Use last stable version tag to stay in a stable release.

Contributing
------------

This projects follows Symfony2 coding standards, so pull requests must pass phpcs
checks. Read more details about
[Symfony2 coding standards](http://symfony.com/doc/current/contributing/code/standards.html)
and install the corresponding [CodeSniffer definition](https://github.com/opensky/Symfony2-coding-standard)
to run code validation.

There is also a policy for contributing to this project. Pull requests must
be explained step by step to make the review process easy in order to
accept and merge them. New features must come paired with PHPUnit tests.

If you would like to contribute, please read the [Contributing Code][1] in the project
documentation. If you are submitting a pull request, please follow the guidelines
in the [Submitting a Patch][2] section and use the [Pull Request Template][3].

[1]: http://symfony.com/doc/current/contributing/code/index.html
[2]: http://symfony.com/doc/current/contributing/code/patches.html#check-list
[3]: http://symfony.com/doc/current/contributing/code/patches.html#make-a-pull-request
