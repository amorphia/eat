<?php
/**
 *  Laravel-Geographical (http://github.com/malhal/Laravel-Geographical)
 *
 *  Created by Malcolm Hall on 4/10/2016.
 *  Copyright © 2016 Malcolm Hall. All rights reserved.
 */

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Geographical
{

    protected $geographical_table;

    /**
     * @param Builder $query
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @return Builder
     */
    public function scopeDistance( $query, $latitude, $longitude, $table = null )
    {

        $this->geographical_table = $table;

        $latName = $this->getQualifiedLatitudeColumn();
        $lonName = $this->getQualifiedLongitudeColumn();

        // Adding already selected columns to query, all columns will be selected by default
        if ($query->getQuery()->columns === null) {
            $query->select($this->getTable() . '.*');
        } else {
            $query->select($query->getQuery()->columns);
        }

        $sql = "((ACOS(SIN(? * PI() / 180) * SIN(" . $latName . " * PI() / 180) + COS(? * PI() / 180) * COS(" .
            $latName . " * PI() / 180) * COS((? - " . $lonName . ") * PI() / 180)) * 180 / PI()) * 60 * ?) as distance";

        $kilometers = false;
        if (property_exists(static::class, 'kilometers')) {
            $kilometers = static::$kilometers;
        }

        if ($kilometers) {
            $query->selectRaw($sql, [$latitude, $latitude, $longitude, 1.1515 * 1.609344]);
        } else {
            // miles
            $query->selectRaw($sql, [$latitude, $latitude, $longitude, 1.1515]);
        }

        //echo $query->toSql();
        //var_export($query->getBindings());
        return $query;
    }

    public function scopeGeofence($query, $latitude, $longitude, $inner_radius, $outer_radius)
    {
        $query = $this->scopeDistance($query, $latitude, $longitude);
        return $query->havingRaw('distance BETWEEN ? AND ?', [$inner_radius, $outer_radius]);
    }

    protected function getQualifiedLatitudeColumn()
    {
        $column = $this->getConnection()->getTablePrefix();
        $column .= $this->geographical_table ?? $this->getTable();
        $column .=  '.' . $this->getLatitudeColumn();

        return $column;
    }

    protected function getQualifiedLongitudeColumn()
    {
        $column = $this->getConnection()->getTablePrefix();
        $column .= $this->geographical_table ?? $this->getTable();
        $column .=  '.' . $this->getLongitudeColumn();

        return $column;
    }

    public function getLatitudeColumn()
    {
        return defined('static::LATITUDE') ? static::LATITUDE : 'latitude';
    }

    public function getLongitudeColumn()
    {
        return defined('static::LONGITUDE') ? static::LONGITUDE : 'longitude';
    }
}

?>
