<?php

namespace CarmineMM\QueryCraft\ETL;

class Transform
{
    /**
     * Transforma un conjunto de datos basado en un mapeo de atributos.
     *
     * Este método es estático para evitar la sobrecarga de instanciar un nuevo objeto
     * en cada iteración del bucle del ETL.
     *
     * @param array $data El array de datos a transformar.
     * @param array $pairAttributes El mapeo de atributos de origen a destino.
     * @return array El array de datos transformados.
     */
    public static function transform(array $data, array $pairAttributes): array
    {
        // Usamos array_map para la iteración principal, ya que es mucho más rápido
        // para arrays grandes que un bucle foreach en PHP puro.
        return array_map(function (array $row) use ($pairAttributes) {
            $transformedRow = [];

            // El bucle interno sobre los atributos es pequeño y rápido.
            foreach ($pairAttributes as $fromKey => $toKeyOrCallable) {
                // Si la clave de origen no existe en la fila, la omitimos.
                if (!array_key_exists($fromKey, $row)) {
                    continue;
                }

                // Maneja transformaciones complejas usando funciones callable.
                if (is_callable($toKeyOrCallable) && $toKeyOrCallable !== 'abs') {
                    $result = $toKeyOrCallable($row);
                    // Se espera que el callable devuelva [nueva_clave, nuevo_valor].
                    if (is_array($result) && count($result) === 2) {
                        $transformedRow[$result[0]] = $result[1];
                    }
                } else {
                    // Ruta común: simple renombrado de clave.
                    $transformedRow[$toKeyOrCallable] = $row[$fromKey];
                }
            }

            return $transformedRow;
        }, $data);
    }
}
