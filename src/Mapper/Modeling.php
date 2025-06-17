<?php

namespace CarmineMM\QueryCraft\Mapper;

use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Facades\DB;
use DateTime;
use DateTimeZone;

/**
 * Model the data, before executing transactions on the database.
 * 
 * @package CarmineMM\QueryCraft
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @license MIT
 * @version 1.0.0
 */
class Modeling
{
    /**
     * Get de data fillable
     *
     * @param Model $model
     * @param array $values
     * @return array
     */
    public static function fillableData(Model $model, array $values): array
    {
        $fillableData = [];

        foreach ($model->getFillable() as $fillable) {
            if (isset($values[$fillable])) {
                $fillableData[$fillable] = $values[$fillable];
            }
        }

        return $fillableData;
    }

    /**
     * Apply timestamps to the data
     *
     * @param Model $model
     * @param array $values
     * @return array
     */
    public static function applyTimeStamps(Model $model, array $values): array
    {
        $insertFields = [];

        // Verificar si se tienen que insertar fields
        if ($model->hasTimestamps()) {
            $date = (new DateTime())
                ->setTimezone(new DateTimeZone(DB::getTimezone()))
                ->format('Y-m-d H:i:s');

            if ($createdField = $model->getCreatedAtField()) {
                $values[$createdField] = $date;
                $insertFields[] = $createdField;
            }

            if ($updatedField = $model->getUpdatedAtField()) {
                $values[$updatedField] = $date;
                $insertFields[] = $updatedField;
            }
        }

        return [
            'values' => $values,
            'insertFields' => $insertFields,
        ];
    }

    /**
     * Updated at
     *
     * @param Model $model
     * @param array $values
     * @return array
     */
    public static function applyUpdatedAt(Model $model, array $values): array
    {
        $insertFields = [];

        if ($model->hasTimestamps() && $model->getUpdatedAtField()) {
            $date = (new DateTime())
                ->setTimezone(new DateTimeZone(DB::getTimezone()))
                ->format('Y-m-d H:i:s');

            $updatedField = $model->getUpdatedAtField();

            $values[$updatedField] = $date;
            $insertFields[] = $updatedField;
        }

        return [
            'values' => $values,
            'insertFields' => $insertFields,
        ];
    }
}
