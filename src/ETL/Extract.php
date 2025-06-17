<?php

namespace CarmineMM\QueryCraft\ETL;

use CarmineMM\QueryCraft\Data\Model;

/**
 * Extract the target database data,
 * This returns the data on Split of 20.000 Rows.
 */
class Extract
{
    /**
     * Offset
     *
     * @var integer
     */
    private int $offset = 0;

    /**
     * Indicate if more extractions are required
     *
     * @var boolean
     */
    public bool $requiredMoreExtract = true;

    /**
     * Indicate which attributes to extract
     *
     * @var array
     */
    protected array $extractAttributes = ['*'];

    /**
     * Construct
     */
    public function __construct(
        protected Model $model,
        protected int $splitIn = 20_000,
        array $extractAttributes = ['*']
    ) {
        $this->extractAttributes = $extractAttributes;
        $this->model->takeSnapshot('etl_base_query');
    }

    /**
     * Indicate how many rows to extract
     *
     * @param int $splitIn
     * @return Extract
     */
    public function setSplitIn(int $splitIn): Extract
    {
        $this->splitIn = $splitIn;
        return $this;
    }

    /**
     * Indicate which attributes to extract
     *
     * @param array $attributes
     * @return Extract
     */
    public function setExtractAttributes(array $attributes): Extract
    {
        $this->extractAttributes = $attributes;
        return $this;
    }

    /**
     * Extract the data
     *
     * @return array
     */
    public function extract(): array
    {
        $this->model->restoreSnapshot('etl_base_query');

        $data = $this->model->limit($this->splitIn, $this->offset)->select($this->extractAttributes)->get();

        if (count($data) < 1) {
            $this->requiredMoreExtract = false;
            return [];
        } else {
            $this->offset += $this->splitIn;
        }

        return $data;
    }
}
