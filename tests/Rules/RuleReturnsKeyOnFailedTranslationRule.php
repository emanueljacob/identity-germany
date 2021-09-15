<?php
namespace Slashplus\IdentityGermany\Tests\Rules;

use Slashplus\IdentityGermany\Validation\ValidatorFactory;
use Illuminate\Contracts\Validation\Rule;


class RuleReturnsKeyOnFailedTranslationRule implements Rule
{
    public function passes($attribute, $value) {
        if($value !== 1)
            return false;

        return true;
    }

    public function message()
    {
        $key = 'validation.custom.notexist';

        return ValidatorFactory::$translator->get($key);
    }
}
