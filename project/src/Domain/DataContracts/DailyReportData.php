<?php
/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 3/28/2020
 * Time: 4:26 PM
 */

namespace CovidTrack\Domain\DataContracts;


class DailyReportData extends DataObjectBase
{
    public static function getFields(): array
    {
        return [
            'id',
            'state',
            'country',
            'date',
            'lat',
            'longitude',
            'bodyCount'
        ];
    }

    public static function createFromArray(array $array)
    {
        return new DailyReportData($array);
    }

    public function __construct(array  $arr)
    {
        $this->_data = $arr;
    }

}