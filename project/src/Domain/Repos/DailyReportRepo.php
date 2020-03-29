<?php
/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 3/28/2020
 * Time: 5:15 PM
 */

namespace CovidTrack\Domain\DataRepos;


use CovidTrack\Domain\DataContracts\DailyReportData;
use CovidTrack\Infrastructure\Databases\MySQL\DailyReportDataMapper;
use CovidTrack\Infrastructure\Databases\MySQL\DataMapperBase;

class DailyReportRepo extends RepoBase
{
    public  function __construct(DailyReportDataMapper $dataMapper)
    {
        parent::__construct($dataMapper, DailyReportData::class);
    }
}