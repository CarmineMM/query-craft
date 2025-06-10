<?php

namespace CarmineMM\QueryCraft\ETL;

use CarmineMM\QueryCraft\Data\Model;

/**
 * Extrae la data de la base de datos objetivo,
 * este devuelve la data en split de 1.000 rows.
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
     * Indica si se requieren mas extracciones
     *
     * @var boolean
     */
    public bool $requiredMoreExtract = true;

    /**
     * Atributos a extraer
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
     * Indica cuantos rows se extraen
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
     * Indica cuantos rows se extraen
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
     * Extrae la data
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
