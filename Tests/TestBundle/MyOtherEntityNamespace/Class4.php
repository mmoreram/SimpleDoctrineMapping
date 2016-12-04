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

namespace Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\MyOtherEntityNamespace;

/**
 * Class Class4.
 */
class Class4
{
    /**
     * @var int
     *
     * Entity id
     */
    protected $id;

    /**
     * @var string
     *
     * Lastname
     */
    protected $lastname;

    /**
     * Class4 constructor.
     *
     * @param int    $id
     * @param string $lastname
     */
    public function __construct(
        int $id,
        string $lastname
    ) {
        $this->id = $id;
        $this->lastname = $lastname;
    }
}
