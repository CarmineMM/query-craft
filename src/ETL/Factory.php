<?php

namespace CarmineMM\QueryCraft\ETL;

use CarmineMM\QueryCraft\Data\Model;

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

        $fromModel->setReturnType($this->extractorReturnType);

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
        $this->extractor->setExtractAttributes($attributes);
        return $this;
    }

    /**
     * Ejecutar ETL
     *
     * @return void
     */
    public function processEtl(): void
    {
        while ($this->extractor->requiredMoreExtract) {
            $data = $this->extractor->extract();
            var_dump($data);
            break;
        }
    }
}
