<?php

namespace Slashplus\IdentityGermany\Contracts\Validation;

interface Validator extends \Illuminate\Contracts\Validation\Validator
{
    public function validatedBirthDate();

    public function validatedExpireDate();
}
