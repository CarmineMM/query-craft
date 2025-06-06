<?php

use CarmineMM\QueryCraft\Data\Model;

class Transform
{
    /**
     * Transformed data
     *
     * @var array
     */
    private array $transformedData;

    /**
     * Constructor
     *
     * @param array $getData
     */
    public function __construct(
        public array $getData,
    ) {
        //
    }

    /**
     * Transform the data
     *
     * @param array $pairAttributes
     * @return void
     */
    public function transform(array $pairAttributes)
    {
        $this->transformedData = [];

        foreach ($this->getData as $data) {
            $transformedData = [];

            foreach ($pairAttributes as $fromData => $toData) {
                $transformedData[$toData] = $data[$fromData];
            }

            $this->transformedData[] = $transformedData;
        }
    }
}
