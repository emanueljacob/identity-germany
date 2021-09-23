<?php

namespace Slashplus\IdentityGermany\Validation\IdCardValidation;

use Slashplus\IdentityGermany\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Arr;
use Slashplus\IdentityGermany\Validation\IdCardValidation\Rules\BlackList;
use Slashplus\IdentityGermany\Validation\IdCardValidation\Rules\Checksum;
use Slashplus\IdentityGermany\Validation\ValidatorFactory;

/**
 * Class Validator
 *
 * @package Slashplus\IdentityGermany\Validation\IdCardValidation
 */
class Validator implements ValidatorContract
{
    /**
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $cardString;

    /**
     * Validator constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        [$this->data, $this->cardString] = $this->parseData($data);
        $factory = new ValidatorFactory();

        /** @var \Illuminate\Validation\Validator validator */
        $this->validator = $factory->make($this->data, $this->rules());
    }

    /**
     * @param $data
     * @return array
     */
    private function parseData($data)
    {
        $subStrData = function (array $data, string $key) {
            return isset($data[$key]) && is_string($data[$key]) ? $data[$key] : '';
        };
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

        $data['full_idcard'] = $this->parseCardString($data);

        return [$data, $data['full_idcard']];
    }


    /**
     * @param array $data
     * @return string
     */
    protected function parseCardString(array $data)
    {
        $idCard = '';
        // the ordering of the following array is relevant!!
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

    /**
     * @param string $string
     * @param array $lengths
     * @return array
     */
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

    /**
     * @return array
     */
    protected function rules()
    {
        $uppercaseRule = function ($attribute, $value, $fail) {
            return strtoupper($value) === $value ?: $fail(':attribute must be uppercase.');
        };
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

    /**
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate()
    {
        return $this->validator->validate();
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return $this->validator->fails();
    }

    /**
     * @return array
     */
    public function failed()
    {
        return $this->validator->failed();
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function errors()
    {
        return $this->validator->errors();
    }

    /**
     * @return mixed
     */
    public function passes()
    {
        return $this->validator->passes();
    }

    /**
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validated()
    {
        return $this->validator->validated();
    }

    /**
     * @return \Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag()
    {
        return $this->validator->getMessageBag();
    }

    /**
     * @param array|string $attribute
     * @param array|string $rules
     * @param callable $callback
     * @return Validator
     */
    public function sometimes($attribute, $rules, callable $callback)
    {
        return $this->validator->sometimes($attribute, $rules, $callback);
    }

    /**
     * @param callable|string $callback
     * @return Validator
     */
    public function after($callback)
    {
        return $this->validator->after($callback);
    }

    /**
     * @param string|null $timezone
     * @return \DateTime|false
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validatedBirthDate(?string $timezone = 'GMT+2')
    {
        return $this->validatedDate('birth', $timezone);
    }

    /**
     * @param string|null $timezone
     * @return \DateTime|false
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validatedExpireDate(?string $timezone = 'GMT+2')
    {
        return $this->validatedDate('expire', $timezone);
    }

    /**
     * @param string $type
     * @return string
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatedDateString(string $type): string
    {
        $valid = $this->validated();
        if(
            array_key_exists($type, $valid)
            && is_array($valid[$type])
            && count(array_intersect_key($valid[$type], array_flip(['year', 'month', 'day']))) === 3
        ) {
            $arr = $valid[$type];
            return "{$arr['year']}{$arr['month']}{$arr['day']}";
        }

        return '';
    }

    /**
     * @param string $type
     * @param string|null $timezone
     * @return \DateTime|false
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatedDate(string $type, ?string $timezone = 'GMT+2')
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
