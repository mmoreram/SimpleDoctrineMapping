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
 * Class MappingExtensionWithoutDriverException.
 */
final class MappingExtensionWithoutDriverException extends Exception
{
    /**
     * Exception constructor.
     *
     * @param string $extension
     */
    public function __construct($extension)
    {
        parent::__construct('Mapping extension "' . $extension . '" is not managed by any driver. Please, consider using xml or yml extensions.');
    }
}
