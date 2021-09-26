<?php

namespace Slashplus\Identity\Tests;

use Slashplus\Identity\Validation\ValidatorFactory;
use Slashplus\Identity\Tests\Rules\isEqualToOneRule;
use Slashplus\Identity\Tests\Rules\RuleReturnsKeyOnFailedTranslationRule;

/** @test */
it('check_data_format_validation', function () {
    $validator = new ValidatorFactory();

    $data = [
        'foo' => 'bar',
    ];

    $rules = [
        'baz' => 'required|url',
    ];

    $validator = $validator->make($data, $rules);
    $errors = $validator->errors()->toArray();

    $this->assertTrue($validator->fails());
    $this->assertEquals('The baz field is required.', $errors['baz'][0]);
});

/** @test */
it('returns_dot_notation_route_to_error_message_if_validation_directory_is_not_found', function () {
    $validator = new ValidatorFactory();

    $result = $validator->translationsRootPath('some/custom/path')->make($data = [], $rules = ['foo' => 'required']);
    $errors = $result->errors()->ToArray();

    $this->assertEquals('validation.required', $errors['foo'][0]);
});

/** @test */
it('allow_custom_translation_directories', function () {
    $validator = new ValidatorFactory();

    $result = $validator
        ->translationsRootPath(__DIR__.'/../src/')
        ->make($data = [], $rules = ['foo' => 'required']);
    $errors = $result->errors()->ToArray();


    $this->assertEquals('The foo field is required.', $errors['foo'][0]);
});

/** @test */
it('check_custom_rules_are_working', function () {
    $validator = new ValidatorFactory();

    $data = ['foo' => 0];
    $rules = ['foo' => new isEqualToOneRule];

    $validator = $validator
        ->translationsRootPath(__DIR__.'/')
        ->make($data, $rules);
    $errors = $validator->errors()->toArray();

    $this->assertTrue($validator->fails());
    $this->assertEquals('the value for foo is not equal to 1', $errors['foo'][0]);
});

/** @test */
it('check_custom_rule_returns_key_on_failed_translation', function () {
    $validator = new ValidatorFactory();

    $data = ['foo' => 0];
    $rules = ['foo' => new RuleReturnsKeyOnFailedTranslationRule];

    $validator = $validator->make($data, $rules);
    $errors = $validator->errors()->toArray();

    $this->assertTrue($validator->fails());
    $this->assertEquals('validation.custom.notexist', $errors['foo'][0]);
});
