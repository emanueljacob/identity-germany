<?php

namespace Slashplus\Identity\Validation\ResidenceValidation\Rules;

use Illuminate\Contracts\Validation\Rule;
use Slashplus\Identity\Validation\ValidatorFactory;

class BlackList implements Rule
{
    protected $blacklist = [
        'Y70101V0376408125F2910312AUS6',
        'Y701001V397708121F2103318TUR6',
        'Y701001V176305213F1203314TUR6',
        'Y70101V0376408125M2910312AUS6',
        'Y701001V397708121M2103318TUR6',
        'Y701001V176305213M1203314TUR6',
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !in_array($value, $this->blacklist);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $key = 'validation.custom.residence.blacklist';
        return ValidatorFactory::$translator->get($key);
    }
}
