<?php
namespace Slashplus\IdentityGermany\Tests;

use Slashplus\IdentityGermany\AgeVerification;
use Slashplus\IdentityGermany\Validation\IdCardValidation\Validator;

it('creates a Id Card Validator', function () {
    expect(AgeVerification::create([], 'id_card'))->toBeInstanceOf(Validator::class);
});

it('throws exception if validator is unknown', function () {
    expect(AgeVerification::create([], 'demo_foo_bar_baz'))->toBeInstanceOf(Validator::class);
})->throws(\Slashplus\IdentityGermany\Exceptions\ClassNotFoundException::class);
