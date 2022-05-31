<?php

namespace Slashplus\Identity\Traits;

trait HandlesDates
{
    /**
     * Create datetime from a short format 'ymd'
     *
     * IMPORTANT INFORMATION: it is absolutely expected that actual years can NOT be determined precisely by only
     * providing the "short" year (schema 'y'; i.e. '39': miht be 1939 or 2039). Therefore note, that this method
     * makes use of "DateTime::createFromFormat) which handles it likes that:
     * Quote "[...] y => A two digit representation of a year (which is assumed to be in the range 1970-2069, inclusive)
     * [...]" see: https://www.php.net/manual/en/datetime.createfromformat.php (2022-31-05)
     *
     * @param string $year two digit representation of a year
     * @param string $month two digit representation of a month
     * @param string $day two digit representation of a day
     * @param string|null $timeZone string representation of a timezone
     * @return \DateTime|false
     */
    public function createDateFromShortFormat(string $year, string $month, string $day, ?string $timeZone = 'GMT+2') {
        $timeZone = is_string($timeZone) ? new \DateTimeZone($timeZone) : null;
        return \DateTime::createFromFormat("ymd H:i:s", "{$year}{$month}{$day} 00:00:00", $timeZone);
    }

    /**
     * Create a plausible date as DateTime object from format 'ymd'
     *
     * "Plausible" DateTime because the year of a 'ymd' string is not precisely defined. Therefore a i.e. 390710
     * could technically be a year of ../1839/1939/2039/..
     * A plausible date in the context of an ID check, is related to a plausible birth date that is likely
     * to be correct by taking the following into account
     * - the lifespan of a human is likely to be around a max of 100yrs (although might be longer, we delimit to 99)
     * - the birth year can not be in the future
     *
     * @param string $dateString in format 'ymd'
     * @param string|null $timeZone
     * @return \DateTime|false
     */
    protected function plausiblePastDateTime(\DateTime $date, ?string $timeZone = 'GMT+2') {
        $timeZone = is_string($timeZone) ? new \DateTimeZone($timeZone) : null;
        $currentDate = new \DateTime('now', $timeZone);

        // considering a human >= 100yrs is not able to proceed
        if ((int)$date->format('Y') > (int) $currentDate->format('Y')) {
            // careful! only do with years, months might be critical because of leap years...
            return $date->sub(\DateInterval::createFromDateString('100 year'));
        }

        return $date;
    }
}
