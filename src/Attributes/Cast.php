<?php

namespace CarmineMM\QueryCraft\Attributes;

use Attribute;

/**
 * Attribute to define how a property should be cast when getting/setting values.
 *
 * Basic usage:
 * #[Cast('int')] - Simple type casting
 * #[Cast('float', [2])] - With options (e.g., decimal places for float)
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Cast
{
    /**
     * The type to cast to (e.g., 'int', 'float', 'datetime').
     *
     * @var string
     */
    public string $type;

    /**
     * Optional parameters to pass to the cast class constructor.
     *
     * @var array
     */
    public array $parameters;

    /**
     * Create a new cast attribute instance.
     *
     * @param string $type The type to cast to
     * @param array $parameters Optional parameters for the cast class
     */
    public function __construct(string $type, array $parameters = [])
    {
        $this->type = $type;
        $this->parameters = $parameters;
    }
}
