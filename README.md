# slashplus identity
Verify the age of a person using the german id card, german passport or 
german residence permission (eAT). 

The verification uses laravels validation package (`illuminate/validation`; version ~6). However it does not require a 
laravel application, so it can be used standalone as library in combination with any package as long as the package 
requirements do not clash.

## Installation
Install the package via composer
`composer require slashplus/identity`

## Usage
To make use of the validation just create a new instance via the \Slashplus\Identity\AgeVerification class that 
acts as concrete factory class, by passing the validation data and method. 
Depending on the validation method (i.e. `id_card`, `passport`, `residence`) 
the correct validator instance will be created and can be used.

i.e. an `id_card` type validation 
```php
// note: this example contains invalid data, but you get an idea
$validator = \Slashplus\Identity\AgeVerification::create([
    [
    'serial' => '5484197598', 
    'birth' => '9512210', 
    'expire' => '4702025', 
    'nationality' => 'D', 
    'checksum' => '4'
    ]
]);
```

You are then able to use all validation methods from the `Slashplus\Identity\Contracts\Validation` 
contract, which extends the `\Illuminate\Contracts\Validation\Validator` contract.

This means you're able to get the following information from the validator if needed:

```php
// regular illuminate methods i.e.
$validatedData = $validator->validated(); // array of validated Data (throws exception)

// additional data i.e.
$validatedBirth = $validator->validatedBirthDate(); // DateTime object (throws exception)
$validatedExpire = $validator->validatedExpireDate(); // DateTime object (throws exception)
```

### Data structure
The input data might depend on the validation type. For more information about it, 
check the types directories and their readme files:

- `id_card`: [src/Validation/IdCardValidation/README.md](/src/Validation/IdCardValidation/README.md)
- `passport`: [src/Validation/PassportValidation/README.md](/src/Validation/PassportValidation/README.md)
- `residence`: [src/Validation/ResidenceValidation/README.md](/src/Validation/ResidenceValidation/README.md)

## Roadmap
Currently, there are only three validation types for checking data against the german id card, the german passport and 
the german residence permission (eAT).
In a further release this might be extended. Feel free to contribute or tell us your wishes. :rocket:

Right now, this package uses components from laravel in version ~6. This is due to the fact that this package should 
be compatible with PHP 7.3 and at the time of creation it must also be compatible to symfony components in versions 
that more recent versions of laravel do not allow to install.
As soon as this is no longer an issue, the required package versions will be updated. Those who might want to fork the 
package should know, that it should be compatible with laravel 6-8 but compatibility can not be guaranteed. 
