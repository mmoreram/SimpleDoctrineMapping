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

namespace Mmoreram\SimpleDoctrineMapping\Tests\TestBundle\Entity;

/**
 * Class Class1.
 */
class Class1
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
     * Code
     */
    protected $code;

    /**
     * Class1 constructor.
     *
     * @param int    $id
     * @param string $code
     */
    public function __construct(
        int $id,
        string $code
    ) {
        $this->id = $id;
        $this->code = $code;
    }

    /**
     * Get Id.
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get Code.
     *
     * @return string
     */
    public function getCode() : string
    {
        return $this->code;
    }
}
