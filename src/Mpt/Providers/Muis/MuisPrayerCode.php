<?php

namespace Mpt\Providers\Muis;

use Mpt\Model\AbstractPrayerCode;

class MuisPrayerCode extends AbstractPrayerCode
{
    /**
     * @return string
     */
    public function getState()
    {
        return 'Singapore';
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return 'Singapore';
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return 'sgp-1';
    }

    /**
     * return string
     */
    public function getCountry()
    {
        return 'SG';
    }

    /**
     * return string
     */
    public function getProviderName()
    {
        return 'muis';
    }
}
