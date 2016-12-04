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

namespace Mmoreram\SimpleDoctrineMapping\Exception;

use Exception;

/**
 * Class EntityManagerNotFoundException.
 */
final class EntityManagerNotFoundException extends Exception
{
    /**
     * Exception constructor.
     *
     * @param string $entityManagerName
     */
    public function __construct($entityManagerName)
    {
        parent::__construct('Entity manager "' . $entityManagerName . '" not found.');
    }
}
