<?php

namespace CarmineMM\QueryCraft\ETL;

use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Facades\DB;
use CarmineMM\QueryCraft\ETL\Transform;
use CarmineMM\QueryCraft\Facades\Debug;
use CarmineMM\UnitsConversion\Conversion\DigitalUnitsConversion;
use CarmineMM\UnitsConversion\Conversion\TimeConversion;

/**
 * ETL Factory
 *
 * @package CarmineMM\QueryCraft\ETL
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @version 1.0.0
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
     * Indicate which attributes to extract
     *
     * @var array
     */
    private array $extractAttributes = [];

    /**
     * Constructor
     *
     * @param Model $from
     * @param Model $to
     */
    public function __construct(
        public Model|string $from,
        public Model|string $to,

        public string $extractorReturnType = 'array',
        public int $chunkSize = 18_000
    ) {
        if (is_string($from)) {
            $instance = new Model();
            $instance->setTable($from);
            $from = $instance;
        }

        if (is_string($to)) {
            $instance = new Model();
            $instance->setTable($to);
            $to = $instance;
        }

        $from
            ->setReturnType($this->extractorReturnType)
            ->setTimestamps(false);

        $to
            ->setReturnType($this->extractorReturnType)
            ->setTimestamps(false);

        $this->extractor = new Extract($from, $this->chunkSize);
    }

    /**
     * Indicate which attributes to extract
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
     * Execute ETL
     *
     * @return void
     */
    public function processEtl($debug = true, $debugQueries = false): void
    {
        $debug = $debug || Debug::getDebugMode();

        if ($debug) {
            Debug::debugMode($debugQueries);
            echo "\nStart Process ETL";
            $startTime = microtime(true);
            $startMemory = memory_get_usage();
            $dataInserted = 0;
        }

        $load = new Load($this->to);

        while ($this->extractor->requiredMoreExtract) {
            //- Extract Data from the source
            $data = $this->extractor->extract();

            if (empty($data)) {
                break;
            }

            //- Transform Data
            $transformed = Transform::transform($data, $this->extractAttributes);

            //- Insert de transformed data
            $load->insert($transformed);

            if ($debug) {
                $dataInserted += count($data);
            }
        }

        if ($debug) {
            $endTime = microtime(true);
            $endMemory = memory_get_usage();

            echo "\nEnd Process ETL ";
            echo "\n\nResumen:";
            echo "\nTime: " . TimeConversion::fromSeconds($endTime - $startTime)->setSymbolMode('long')->smartConversion();
            echo "\nMemory: " . DigitalUnitsConversion::fromBytes(($endMemory - $startMemory))->setSymbolMode('short')->smartConversion();
            echo "\nData Inserted: {$dataInserted}";
            Debug::debugMode($debugQueries);
        }
    }
}
