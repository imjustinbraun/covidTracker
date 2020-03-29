<?php
/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 3/28/2020
 * Time: 5:25 PM
 */

namespace CovidTrack\Infrastructure\Databases\MySQL;


use CovidTrack\Domain\DataContracts\DailyReportData;
use Opulence\Databases\ConnectionPools\ConnectionPool;

class DailyReportDataMapper extends DataMapperBase
{
    protected function loadEntity(array $hash)
    {
        return DailyReportData::createFromArray($hash);
    }

    public function __construct(ConnectionPool $connectionPool)
    {
        $this->_idFieldName = 'id';
        $this->_tableName = 'DailyReports';
        parent::__construct($connectionPool);
    }

}