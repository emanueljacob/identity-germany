<?php

namespace Slashplus\IdentityGermany;

use Slashplus\IdentityGermany\Contracts\Validation\Validator;
use Slashplus\IdentityGermany\Exceptions\ClassNotFoundException;

class AgeVerification
{
    public static function create(array $data, string $validator = 'id_card')
    {
        $className = \Illuminate\Support\Str::studly($validator);
        $class = '\\'.__NAMESPACE__.'\\Validation\\'.$className.'Validation\\Validator';
        if (class_exists($class) && class_implements(Validator::class)) {
            return new $class($data);
        }

        throw new ClassNotFoundException("There is no Validation class with name '{$class}'", $class);
    }
}
