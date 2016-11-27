<?php

use Geocoder\Geocoder;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\Country;
use Mpt\Exception\DataNotAvailableException;
use Mpt\Exception\InvalidCodeException;
use Mpt\Exception\ProviderException;
use Mpt\Providers\Muis\MuisProvider;

class MuisProviderTest extends \PHPUnit\Framework\TestCase
{

    public function testInCountryCoordinates()
    {
        $geocoder = $this->getMockBuilder(Geocoder::class)
            ->getMock();

        $geocoder->expects($this->once())
            ->method('reverse')
            ->willReturn(new AddressCollection([
                new Address(null, null, null, null, null, 'Singapore', null, null,
                    new Country('Singapore', 'SG'))
            ]));

        $mp = $this->getMuisProvider($geocoder);

        $result = $mp->getCodeByCoordinates(1.3147268, 103.8116508);
        $this->assertEquals('sgp-1', $result);
    }

    public function testOutsideCountryCoordinates()
    {
        $geocoder = $this->getMockBuilder(Geocoder::class)
            ->getMock();

        $geocoder->expects($this->once())
            ->method('reverse')
            ->willReturn(new AddressCollection([
                new Address(null, null, null, null, null, 'Bagan Serai', null, null,
                    new Country('Malaysia', 'MY'))
            ]));

        $mp = $this->getMuisProvider($geocoder);

        $this->expectException(DataNotAvailableException::class);
        $mp->getCodeByCoordinates(5.00983, 100.647);
    }

    public function testValidCodes()
    {
        $mp = $this->getMuisProvider();

        $data = $mp->setMonth(11)
            ->setYear(2016)
            ->getTimesByCode('sgp-1');

        $this->assertEquals(1477949220, $data->getTimes()[0][0]);
    }

    public function testInvalidCodes()
    {
        $mp = $this->getMuisProvider();
        $this->expectException(InvalidCodeException::class);
        $mp->getTimesByCode('ext-153');
    }

    public function testUnavailableData()
    {
        $mp = $this->getMuisProvider();

        $this->expectException(DataNotAvailableException::class);

        $mp->setMonth(1)
            ->setYear(2013)
            ->getTimesByCode('sgp-1')
            ->getTimes();
    }

    private function getMuisProvider($geocoder = null)
    {
        if (is_null($geocoder)) {
            $geocoder = $this->getMockBuilder(Geocoder::class)
                ->getMock();
        }

        return new MuisProvider($geocoder);
    }
}
