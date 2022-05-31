<?php

namespace Slashplus\Identity\Tests;

use Illuminate\Support\Str;
use Slashplus\Identity\Traits\HandlesDates;

uses(HandlesDates::class);

it('creates datetime with correct years from correctly', function($year, $parameters, $expectation) {
    $dateTime = $this->createDateFromShortFormat($year, ...array_values($parameters));
    expect($dateTime)->toBeInstanceOf(\DateTime::class);
    expect($dateTime->format('Y-m-d'))->toBe($expectation);
})->with(
    function () {
        for ($i = 0; $i <= 99; $i++) {
            $shortYear = str_pad((int)$i,2,"0", STR_PAD_LEFT);

            // "y => A two digit representation of a year (which is assumed to be in the range 1970-2069, inclusive)"
            // see: https://www.php.net/manual/de/datetime.createfromformat.php
            $expectedYear = (int)$shortYear >= 70 ? "19{$shortYear}": "20{$shortYear}";
            yield [$shortYear, ['month' => '01', 'day' => '20', 'timezone' => 'GMT+2', 'format' => 'ymd'], "{$expectedYear}-01-20"];
        }
    }
);

it('modifies created datetime to be plausible in the past', function($year, $parameters, $expectation) {
    $dateTime = $this->createDateFromShortFormat($year, ...array_values($parameters));
    $this->plausiblePastDateTime($dateTime, $parameters['timezone']);
    expect($dateTime->format('Y-m-d'))->toBe($expectation);
})->with(
    function () {
        $timeZone = 'GMT+2';
        $currentYear = (int)(new \DateTime('now', new \DateTimeZone($timeZone)))->format('Y');
        $futureCentury = $currentYear + 100;
        for ($i = (int)$currentYear; $i <= $futureCentury; $i++) {
            $shortYear = Str::substr((string)$i, 2,2);
            $expectedYear = $i <= $currentYear ? $i : $i - 100;

            yield [$shortYear, ['month' => '01', 'day' => '01', 'timezone' => $timeZone, 'format' => 'ymd'], "{$expectedYear}-01-01"];
        }
    }
);
