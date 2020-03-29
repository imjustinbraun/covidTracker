<?php
namespace CovidTrack\Application\Console\Commands;

use CovidTrack\Domain\DataContracts\DailyReportData;
use CovidTrack\Domain\DataRepos\DailyReportRepo;
use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;

/**
 * Defines an example "Hello, world" command
 */
class DataImportCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('data:import')
            ->setDescription('Imports Data from files and updates the database');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $response->writeln('Initializing Import');
        $path = 'C:\Users\Justin\Desktop\Github\COVID-19\csse_covid_19_data\csse_covid_19_daily_reports';
        $dir = new \DirectoryIterator($path);
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {

                $file = fopen($fileinfo->getPathname(), 'r');

                $z = 0;
                $isNewFormat = false;
                while (($data = fgetcsv($file)) !== FALSE)
                {
                    if ($z == 0)
                    {
                        $isNewFormat = ($data[0] === 'FIPS');
                        $z++;
                        continue;
                    }

                    $row = [];
                    if ($isNewFormat)
                    {
                        $row = [
                            'state' => $data[2],
                            'country' => $data[3],
                            'date' => $data[4],
                            'lat' => $data[5],
                            'longitude' => $data[6],
                            'bodyCount' => ($data[7] === '') ? 0 : $data[7]
                        ];
                    }
                    else
                    {
                        $row = [
                            'state' => $data[0],
                            'country' => $data[1],
                            'date' => $data[2],
                            'lat' => $data[6] ?? null,
                            'longitude' => $data[7] ?? null,
                            'bodyCount' => ($data[4] === '') ? 0 : $data[4]
                        ];
                    }

                    print_r($row);
                    $this->_repo->addOrUpdate(DailyReportData::createFromArray($row));

                }
            }
        }
    }

    private $_repo = null;
    public function __construct(DailyReportRepo $repo)
    {
        $this->_repo = $repo;
        parent::__construct();
    }
}
