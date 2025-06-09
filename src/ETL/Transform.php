<?php

namespace CarmineMM\QueryCraft\ETL;

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
     * @return array
     */
    public function transform(array $pairAttributes): array
    {
        $this->transformedData = [];


        foreach ($this->getData as $data) {
            $transformedData = [];

            foreach ($pairAttributes as $fromData => $toData) {
                if (is_callable($toData)) {
                    $result = $toData($data);
                    $transformedData[$result[0]] = $result[1];
                } else {
                    $transformedData[$toData] = $data[$fromData];
                }
            }

            $this->transformedData[] = $transformedData;
        }

        return $this->transformedData;
    }
}
