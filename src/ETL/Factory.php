<?php

namespace CarmineMM\QueryCraft\ETL;

use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\ETL\Transform;

/**
 * Constructor para obtener la data
 */
class Factory
{
    /**
     * Extractor
     *
     * @var Extract
     */
    private Extract $extractor;

    /**
     * Define que atributos extraer
     *
     * @var array
     */
    private array $extractAttributes = [];

    /**
     * Constructor
     *
     * @param Model $fromModel
     * @param Model $toModel
     */
    public function __construct(
        public Model|string $fromModel,
        public Model|string $toModel,

        public string $extractorReturnType = 'array',
        public int $splitIn = 1_000
    ) {
        if (is_string($fromModel)) {
            $instance = new Model();
            $instance->setTable($fromModel);
            $fromModel = $instance;
        }

        if (is_string($toModel)) {
            $instance = new Model();
            $instance->setTable($toModel);
            $toModel = $instance;
        }

        $fromModel
            ->setReturnType($this->extractorReturnType)
            ->setTimestamps(false);

        $toModel
            ->setReturnType($this->extractorReturnType)
            ->setTimestamps(false);

        $this->extractor = new Extract($fromModel);

        $this->extractor->setSplitIn($this->splitIn);
    }

    /**
     * Indica que atributos específicos se van a extraer
     *
     * @param array $attributes
     * @return Factory
     */
    public function extractAttributes(array $attributes): Factory
    {
        $this->extractAttributes = $attributes;
        return $this;
    }

    /**
     * Ejecutar ETL
     *
     * @return void
     */
    public function processEtl(): void
    {
        echo "\nStart Process ETL";
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        while ($this->extractor->requiredMoreExtract) {
            //- Extract Data from the source
            $data = $this->extractor->extract();

            //- Transform Data
            $transformed = (new Transform($data))->transform($this->extractAttributes);

            //- Insert de transformed data
            $load = (new Load($this->toModel))->insert($transformed);

            break;
        }

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        echo "\nEnd Process ETL";
        echo "\nTime: " . ($endTime - $startTime);
        echo "\nMemory: " . ($endMemory - $startMemory);
    }
}
