<?php

namespace Slashplus\IdentityGermany\Validation\IdCardValidation\Rules;

use Illuminate\Contracts\Validation\Rule;

class BlackList implements Rule
{
    protected array $blacklist = [
        '122000129764081251710319D8',
        'L01X00T47183081263108011D7',
        'T22000129364081252010315D4',
        'TTTT00001364120871010318D2',
        'L0L0016W7464081251010318D4',
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
        return ':attribute is one of a blacklisted id cards (i.e. a demo card)';
    }
}
