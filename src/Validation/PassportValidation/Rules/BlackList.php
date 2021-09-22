<?php

namespace Slashplus\IdentityGermany\Validation\PassportValidation\Rules;

use Illuminate\Contracts\Validation\Rule;

class BlackList implements Rule
{
    protected $blacklist = [
        '12200012976408125F1710319D8',
        'L01X00T4718308126F3108011D7',
        'T2200012936408125F2010315D4',
        'TTTT0000136412087F1010318D2',
        'L0L0016W746408125F1010318D4',
        '12200012976408125M1710319D8',
        'L01X00T4718308126M3108011D7',
        'T2200012936408125M2010315D4',
        'TTTT0000136412087M1010318D2',
        'L0L0016W746408125M1010318D4',
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
