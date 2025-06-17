<?php

namespace CarmineMM\QueryCraft\ETL;

class Transform
{
    /**
     * Transform a set of data based on an attribute mapping.
     *
     * This method is static to avoid the overhead of instantiating a new object
     * in each iteration of the ETL loop.
     *
     * @param array $data The array of data to transform.
     * @param array $pairAttributes The mapping of attributes from source to destination.
     * @return array The array of transformed data.
     */
    public static function transform(array $data, array $pairAttributes): array
    {
        // Use array_map for the main iteration, as it is much faster
        // for large arrays than a foreach loop in pure PHP.
        return array_map(function (array $row) use ($pairAttributes) {
            $transformedRow = [];

            // The internal loop over attributes is small and fast.
            foreach ($pairAttributes as $fromKey => $toKeyOrCallable) {
                // If the source key does not exist in the row, skip it.
                if (!array_key_exists($fromKey, $row)) {
                    continue;
                }

                // Handle complex transformations using callable functions.
                if (is_callable($toKeyOrCallable) && $toKeyOrCallable !== 'abs') {
                    $result = $toKeyOrCallable($row);
                    // Expect the callable to return [new_key, new_value].
                    if (is_array($result) && count($result) === 2) {
                        $transformedRow[$result[0]] = $result[1];
                    }
                } else {
                    // Common path: simple key renaming.
                    $transformedRow[$toKeyOrCallable] = $row[$fromKey];
                }
            }

            return $transformedRow;
        }, $data);
    }
}
