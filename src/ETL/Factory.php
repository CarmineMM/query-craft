<?php

namespace CarmineMM\QueryCraft\ETL;

use CarmineMM\QueryCraft\Data\Model;

/**
 * Constructor para obtener la data
 */
class Factory
{
    /**
     * Indica cual es el limite de rows por split
     *
     * @var integer
     */
    private int $splitIn = 1_000;

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
        public Model $fromModel,
        public Model $toModel,

        public string $extractorReturnType = 'array',
    ) {
        $fromModel->setReturnType($this->extractorReturnType);

        $this->extractor = new Extract($fromModel);
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
}
