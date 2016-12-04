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

namespace Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\DependencyInjection;

use Mmoreram\BaseBundle\DependencyInjection\BaseExtension;

/**
 * Class TestExtension.
 */
class TestExtension extends BaseExtension
{
    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public function getAlias()
    {
        return 'test';
    }

    /**
     * Load Parametrization definition.
     *
     * return array(
     *      'parameter1' => $config['parameter1'],
     *      'parameter2' => $config['parameter2'],
     *      ...
     * );
     *
     * @param array $config Bundles config values
     *
     * @return array
     */
    protected function getParametrizationValues(array $config) : array
    {
        return [
            'testbundle.class3.class' => 'Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\MyOtherEntityNamespace\Class3',
            'testbundle.class3.mapping_file' => '@TestBundle/Mapping/Class3.orm.yaml',
            'testbundle.class3.enable' => true,

            'testbundle.class4.class' => 'Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\MyOtherEntityNamespace\Class4',
            'testbundle.class4.mapping_file' => '@TestBundle/Mapping/Class4.orm.xml',
            'testbundle.class4.entity_manager' => 'alternative',
        ];
    }
}
