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
     * Indica cual es el limite de rows por split
     *
     * @var integer
     */
    private int $splitIn = 1_000;

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
    private array $extractAttributes = ['*'];

    /**
     * Construct
     */
    public function __construct(
        public Model $model
    ) {
        //
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
        $data = $this->model->limit($this->splitIn, $this->offset)->get($this->extractAttributes);
        var_dump($data);

        if (count($data) < 1) {
            $this->requiredMoreExtract = false;
        } else {
            $this->offset += $this->splitIn;
        }

        return $data;
    }
}
