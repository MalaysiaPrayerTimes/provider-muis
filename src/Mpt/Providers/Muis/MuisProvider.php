<?php

namespace Mpt\Providers\Muis;

use Geocoder\Model\Address;
use Mpt\Exception\DataNotAvailableException;
use Mpt\Exception\InvalidCodeException;
use Mpt\Model\PrayerData;
use Mpt\Providers\BaseProvider;

class MuisProvider extends BaseProvider
{
    public function getName(): string
    {
        return 'muis';
    }

    public function getCodeByCoordinates($lat, $lng, int $acc = 0): string
    {
        /** @var Address[] $results */
        $results = $this->reverseGeocode($lat, $lng);
        $code = null;

        if (empty($results)) {
            throw new DataNotAvailableException('No results returned from geocoder.');
        }

        foreach ($results as $address) {
            if ($this->isInCountry($address, 'SG')) {
                return 'sgp-1';
            }
        }

        throw new DataNotAvailableException('Not in Singapore.');
    }

    public function getTimesByCode(string $code): PrayerData
    {
        if ($code != 'sgp-1') {
            throw new InvalidCodeException();
        }

        $data = new MuisPrayerData();

        return $data->setMonth($this->getMonth())
            ->setYear($this->getYear());
    }

    public function getSupportedCodes(): array
    {
        return [
            new MuisPrayerCode(),
        ];
    }
}
