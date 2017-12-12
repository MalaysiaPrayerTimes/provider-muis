<?php

namespace Mpt\Providers\Muis;

use Mpt\Exception\DataNotAvailableException;
use Mpt\Exception\ProviderException;
use Mpt\Model\AbstractPrayerData;

class MuisPrayerData extends AbstractPrayerData
{
    private $sources = [
        '2016' => 'http://www.muis.gov.sg/documents/Resource_Centre/Prayer_Timetable_2016.pdf',
        '2017' => 'http://www.muis.gov.sg/documents/Resource_Centre/Prayer%20Timetable%202017.pdf',
        '2018' => 'https://www.muis.gov.sg/documents/Prayer%20Timetable%202018.pdf',
    ];

    /**
     * @return string
     */
    public function getCode()
    {
        return 'sgp-1';
    }

    /**
     * @return array
     */
    public function getTimes()
    {
        $parsed_times = [];

        $year = $this->getYear();
        $month = $this->getMonth();
        $month_day_count = date('t');

        $handle = fopen($this->getFile(), 'r');

        if (!$handle) {
            throw new ProviderException('Error reading MUIS files.');
        }

        $day_count = 0;

        while (!feof($handle)) {
            $buffer = fgetcsv($handle);

            $date = $buffer[0];

            $times = [
                $buffer[2],
                $buffer[3],
                $buffer[4],
                $buffer[5],
                $buffer[6],
                $buffer[7],
            ];

            preg_match("/(\d{1,2})\/$month\/$year/", $date, $output);

            if (empty($output)) {
                continue;
            }

            $day_count++;

            if ($day_count > $month_day_count) {
                break;
            }

            $day = $output[1];
            $day_times = [];

            for ($i = 0; $i < 6; $i++) {
                $c = explode(' ', $times[$i]);
                $ch = (int) $c[0];
                $cm = (int) $c[1];

                if ($i > 2) {
                    if ($i === 3) {
                        if ($ch < 11) {
                            $ch += 12;
                        }
                    } else {
                        if ($ch < 12) {
                            $ch += 12;
                        }
                    }
                } else {
                    if ($ch >= 12) {
                        $ch -= 12;
                    }
                }

                $t = mktime($ch, $cm, 0, $month, $day, $year);
                $day_times[] = $t;
            }

            $parsed_times[] = $day_times;
        }

        fclose($handle);

        return $parsed_times;
    }

    /**
     * @return string
     */
    public function getPlace()
    {
        return 'Singapore';
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return 'muis';
    }

    /**
     * @return \DateTime
     */
    public function getLastModified()
    {
        return \DateTime::createFromFormat('U', filemtime($this->getFile()));
    }

    /**
     * return string
     */
    private function getFile()
    {
        $year = $this->getYear();

        $file = __DIR__ . "/Resources/$year.csv";

        if (!file_exists($file)) {
            throw new DataNotAvailableException("No data available for year $year.");
        }

        return $file;
    }

    /**
     * @return array
     */
    public function getExtraAttributes()
    {
        return [
            'muis_source' => $this->sources[$this->getYear()],
        ];
    }
}
