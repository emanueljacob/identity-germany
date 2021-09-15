<?php

namespace Slashplus\IdentityGermany\Validation\IdCardValidation;

use Slashplus\IdentityGermany\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Arr;
use Slashplus\IdentityGermany\Validation\IdCardValidation\Rules\BlackList;
use Slashplus\IdentityGermany\Validation\IdCardValidation\Rules\Checksum;
use Slashplus\IdentityGermany\Validation\ValidatorFactory;

class Validator implements ValidatorContract
{
    protected \Illuminate\Validation\Validator $validator;

    protected array $data;

    protected string $idCardString;

    public function __construct(array $data)
    {
        [$this->data, $this->idCardString] = $this->parseData($data);
        $factory = new ValidatorFactory();

        $this->validator = $factory->make($this->data, $this->rules());
    }

    private function parseData($data)
    {
        $subStrData = fn(array $data, string $key) => isset($data[$key]) && is_string($data[$key]) ? $data[$key] : '';
        foreach (['birth', 'expire'] as $date) {
            $data[$date] = $this->explodeChunks($subStrData($data, $date), [
                    'year' => 2,
                    'month' => 2,
                    'day' => 2,
                    'checksum' => null, // the rest (should be 1, but will be validated)
                ]
            );
        }
        $data['serial'] = $this->explodeChunks($subStrData($data, 'serial'), [
            'authority' => 4,
            'consecutive' => 5,
            'checksum' => null, // the rest (should be 1, but will be validated)
        ]);

        $data['full_idcard'] = $this->parseIdCardString($data);

        return [$data, $data['full_idcard']];
    }


    protected function parseIdCardString(array $data)
    {
        $idCard = '';
        $keys = [
            'serial.authority',
            'serial.consecutive',
            'serial.checksum',
            'birth.year',
            'birth.month',
            'birth.day',
            'birth.checksum',
            'expire.year',
            'expire.month',
            'expire.day',
            'expire.checksum',
            'nationality',
            'checksum',
        ];
        foreach ($keys as $key) {
            $idCard .= Arr::get($data, $key, '');
        }

        return $idCard;
    }

    protected function explodeChunks(string $string, array $lengths)
    {
        $index = 0;
        $arr = [];
        foreach ($lengths as $key => $length) {
            $arr[$key] = mb_substr($string, $index, $length, 'UTF-8');
            $index += $length;
        }

        return $arr;
    }

    const YYMMDD = '/^\d{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])$/';


    protected function rules()
    {
        $uppercaseRule = fn(
            $attribute,
            $value,
            $fail
        ) => strtoupper($value) === $value ?: $fail(':attribute must be uppercase.');
        $regexAllowedChars = '/^[[0-9CFGHJKLMNPRTVWXYZ]+$/i';
        $regexAllowedYear = '/^[0-9]{2}$/i';
        $regexAllowedMonth = '/^(0[1-9]|1[012])$/i';
        $regexAllowedDay = '/^(0[1-9]|[12]\d|3[01])$/i';
        return [
            'serial' => ['required', 'array'],
            'serial.authority' => ['required', 'string', "regex:{$regexAllowedChars}", "size:4"],
            'serial.consecutive' => ['required', 'string', "regex:{$regexAllowedChars}", "size:5"],
            'serial.checksum' => [
                'required', 'string', 'regex:/^[0-9]$/i', "size:1",
                new Checksum($this->data, ['serial.authority', 'serial.consecutive']),
            ],
            'birth' => ['required', 'array'],
            'birth.year' => ['required', 'string', "regex:{$regexAllowedYear}", "size:2"],
            'birth.month' => ['required', 'string', "regex:{$regexAllowedMonth}", "size:2"],
            'birth.day' => ['required', 'string', "regex:{$regexAllowedDay}", "size:2"],
            'birth.checksum' => [
                'required_with_all:birth.year', 'string', 'regex:/^[0-9\s]+$/i', "size:1",
                new Checksum($this->data, ['birth.year', 'birth.month', 'birth.day']),
            ],
            'expire' => ['required', 'array'],
            'expire.year' => ['required', 'string', "regex:{$regexAllowedYear}", "size:2"],
            'expire.month' => ['required', 'string', "regex:{$regexAllowedMonth}", "size:2"],
            'expire.day' => ['required', 'string', "regex:{$regexAllowedDay}", "size:2"],
            'expire.checksum' => [
                'required', 'string', 'regex:/^[0-9]$/i', "size:1",
                new Checksum($this->data, ['expire.year', 'expire.month', 'expire.day']),
            ],
            'nationality' => ['required', 'string', 'alpha_num', $uppercaseRule],
            'checksum' => ['required', 'string', 'regex:/^[0-9]$/i', "size:1"],
            'full_idcard' => ['required', 'string', "size:26", new BlackList]
            // will be created automatically from single fields
        ];
    }

    public function validate()
    {
        return $this->validator->validate();
    }

    public function fails()
    {
        return $this->validator->fails();
    }

    public function failed()
    {
        return $this->validator->failed();
    }

    public function errors()
    {
        return $this->validator->errors();
    }

    public function passes()
    {
        return $this->validator->passes();
    }

    public function validated()
    {
        return $this->validator->validated();
    }

    public function getMessageBag()
    {
        return $this->validator->getMessageBag();
    }

    public function sometimes($attribute, $rules, callable $callback)
    {
        return $this->validator->sometimes($attribute, $rules, $callback);
    }

    public function after($callback)
    {
        return $this->validator->after($callback);
    }

    public function validatedBirthDate()
    {
        return $this->validatedDate('birth');
    }

    public function validatedExpireDate()
    {
        return $this->validatedDate('expire');
    }

    protected function validatedDate(string $type, ?string $timezone = 'GMT')
    {
        $valid = $this->validated();
        if(
            array_key_exists($type, $valid)
            && is_array($valid[$type])
            && count(array_intersect_key($valid[$type], array_flip(['year', 'month', 'day']))) === 3
        ) {
            $arr = $valid[$type];
            $timezone = is_string($timezone) ? new \DateTimeZone($timezone) : null;
            return \DateTime::createFromFormat('ymd H:i:s', "{$arr['year']}{$arr['month']}{$arr['day']} 00:00:00", $timezone);
        }

        return false;
    }
}
