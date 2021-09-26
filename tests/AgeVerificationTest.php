<?php

namespace Slashplus\Identity\Tests;

use Slashplus\Identity\AgeVerification;
use Slashplus\Identity\Validation\IdCardValidation\Validator as IdCardValidator;
use Slashplus\Identity\Validation\PassportValidation\Validator as PassportValidator;
use Slashplus\Identity\Validation\ResidenceValidation\Validator as ResidenceValidator;

it('creates a IdCardValidator', function () {
    expect(AgeVerification::create([], 'id_card'))->toBeInstanceOf(IdCardValidator::class);
});

it('creates a PassportValidator', function () {
    expect(AgeVerification::create([], 'passport'))->toBeInstanceOf(PassportValidator::class);
});

it('creates a ResidenceValidator', function () {
    expect(AgeVerification::create([], 'residence'))->toBeInstanceOf(ResidenceValidator::class);
});

it('throws exception if validator is unknown', function () {
    AgeVerification::create([], 'demo_foo_bar_baz');
})->throws(\Slashplus\Identity\Exceptions\ClassNotFoundException::class);


it('throws no exception if validator type is known', function ($type) {
    try{
        AgeVerification::create([], $type);
        $this->assertTrue(true);
    }catch (\Exception $e){
        $this->fail("Exception has been thrown: {$e->getMessage()}" );
    }
})->with(['id_card', 'passport', 'residence']);
