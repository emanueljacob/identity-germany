<?php

namespace Slashplus\IdentityGermany\Contracts\Validation;

interface Validator extends \Illuminate\Contracts\Validation\Validator
{
    public function validatedBirthDate(?string $timezone = 'GMT+2');

    public function validatedExpireDate(?string $timezone = 'GMT+2');
}
