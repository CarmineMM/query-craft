<?php

namespace CarmineMM\QueryCraft\Casts;

use CarmineMM\QueryCraft\Contracts\Casts as ContractsCasts;
use CarmineMM\QueryCraft\Data\Model;

abstract class NumericCast implements ContractsCasts
{
    /**
     * The number of decimal places to use.
     *
     * @var int
     */
    protected int $decimals;
    
    /**
     * Create a new numeric cast instance.
     *
     * @param int $decimals
     */
    public function __construct(int $decimals = 0)
    {
        $this->decimals = $decimals;
    }

    /**
     * Format a numeric value with the specified number of decimals.
     *
     * @param mixed $value
     * @return string
     */
    protected function formatNumber($value): string
    {
        return number_format((float)$value, $this->decimals, '.', '');
    }

    /**
     * Handle dynamic method calls to the cast.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this, $method)) {
            return $this->$method(...$parameters);
        }

        throw new \BadMethodCallException("Method [{$method}] does not exist.");
    }
}
