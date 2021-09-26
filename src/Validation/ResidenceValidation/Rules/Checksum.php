<?php

namespace Slashplus\Identity\Validation\ResidenceValidation\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Slashplus\Identity\Validation\ValidatorFactory;

class Checksum implements Rule
{

    private $dependentFields;
    private $data;
    private $valueMap = [
        0 => 0,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        'C' => 12,
        'F' => 15,
        'G' => 16,
        'H' => 17,
        'J' => 19,
        'K' => 20,
        'L' => 21,
        'M' => 22,
        'N' => 23,
        'P' => 25,
        'R' => 27,
        'T' => 29,
        'V' => 31,
        'W' => 32,
        'X' => 33,
        'Y' => 34,
        'Z' => 35,
    ];

    private $multiplierMap = [7, 3, 1];

    public function __construct($data, array $dependentFields)
    {
        $this->data = $data;
        $this->dependentFields = $dependentFields;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->dependentFieldsStrExist() && $this->validateChecksum($value);
    }

    protected function dependentFieldsStrExist(){
        foreach ($this->dependentFields as $dependentField){
            if(!Arr::has($this->data, $dependentField) && is_string(Arr::get($this->data, $dependentField))){
                return false;
            }
        }

        return true;
    }

    protected function getDependentFieldsString(){
        $val = [];
        foreach ($this->dependentFields as $dependentField){
            $val[] = Arr::get($this->data, $dependentField);
        }

        return implode('',$val);
    }

    protected function validateChecksum(string $checksum){
        $blockDigits = str_split($this->getDependentFieldsString());
        $sum = 0;
        foreach ($blockDigits as $index => $value) {
            if(!isset($this->valueMap[$value])){
                return -1; // stop because the value is invalid... validation of dependent field will throw error then
            }
            $sum += $this->valueMap[$value] * $this->multiplierMap[$index % 3];
        }

        return $sum % 10 == $checksum;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $key = 'validation.custom.checksum';
        return ValidatorFactory::$translator->get($key);
    }
}
