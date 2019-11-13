<?php

namespace Bumerok91\Directions;

/**
 * Class Action
 * @package Bumerok91\Directions
 */
final class Action
{
    /** @var string */
    public $type;

    /** @var float */
    public $value;

    /**
     * Action constructor.
     * @param string $type
     * @param float $value
     */
    public function __construct(string $type, float $value)
    {
        $this->type = $type;
        $this->value = $value;
    }
}