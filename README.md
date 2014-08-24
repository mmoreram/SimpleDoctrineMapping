Simple Doctrine Mapping for Symfony2
=====

*KISS*, Remember?

This bundle streamlines and reduces the complexity of the Doctrine mapping process. the
first premise to use this bundle is disable, if enabled, the
`auto_mapping` config definition of your managers. Once active, hands-on
work, we will define specificly how your entities map to your ORM.

SimpleDoctrineMapping offers you just an abstract compiler pass with one method,
enough to make your project work.

Repeat with me, *Keep it simple*

CompilerPass
------------

A CompilerPass, to those of you who still do not know what are they, try to see
them as your last chance to configure your container. At this point you can
retrieve all your parameter configuration, but you cannot build any service, you
is the point where you can dinamically build and complete services.

Once compiled, this container will be read-only.

This CompilerPass let each bundle be responsable for itw own entities, defining
per each one, the class to be mapped, the path of the mapping file and the
manager that will manage it.

You should create your own compiler pass


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

and pass it to your bundle. Like this.

``` php
    <?php

    /**
     * SimpleDoctrineMapping for Symfony2
     */

    namespace Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle;

    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\HttpKernel\Bundle\Bundle;

    use Mmoreram\SimpleDoctrineMapping\Tests\Functional\TestBundle\CompilerPass\MappingCompilerPass;

    /**
     * Class TestBundle
     */
    class TestBundle extends Bundle
    {
        /**
         * @param ContainerBuilder $container
         */
        public function build(ContainerBuilder $container)
        {
            parent::build($container);

            $container->addCompilerPass(new MappingCompilerPass());
        }
    }
```

And that's it. After the container compilation we will add our mapping
information. No magic.


addEntityMapping()
------------------

The method *addEntityMapping()* offers us not much options, but the necessary
to be able to define the entity map of most cases.


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

Of course, all values are required.

Parameters
----------

So, imagine that you are working in a public Bundle, I mean, your bundle will be
installed by other projects in their vendor folder, but you want to expose your
own entity model.

You could think that using this bundle you force the final user to use your
model implementation in all cases, but is not.

If you want to give this power to your users, if you want to expose overridable
entities, you can define your model using container parameters.

``` yml
parameters:

    #
    # Classes
    #
    test_bundle.entity.user.class: "TestBundle\Entity\User"
    test_bundle.entity.user.mapping_file_path: "@TestBundle/Mapping/Class.orm.yml"
    test_bundle.entity.user.entity_manager: default
```

In that case your bundle will put at the mercy of the users the ability to
override all the required parameters just overriding specific configuration
items.

You must finally create these params with your default values.

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
