<?php

namespace Slashplus\IdentityGermany\Tests;

use Slashplus\IdentityGermany\AgeVerification;
use Slashplus\IdentityGermany\Validation\IdCardValidation\Validator as IdCardValidator;
use Slashplus\IdentityGermany\Validation\PassportValidation\Validator as PassportValidator;

it('creates a IdCardValidator', function () {
    expect(AgeVerification::create([], 'id_card'))->toBeInstanceOf(IdCardValidator::class);
});

it('creates a PassportValidator', function () {
    expect(AgeVerification::create([], 'passport'))->toBeInstanceOf(PassportValidator::class);
});

it('throws exception if validator is unknown', function () {
    AgeVerification::create([], 'demo_foo_bar_baz');
})->throws(\Slashplus\IdentityGermany\Exceptions\ClassNotFoundException::class);


it('throws no exception if validator type is known', function ($type) {
    try{
        AgeVerification::create([], $type);
        $this->assertTrue(true);
    }catch (\Exception $e){
        $this->fail("Exception has been thrown: {$e->getMessage()}" );
    }
})->with(['id_card', 'passport']);
