# slashplus identity-germany
Verify the age of a person using the german id card. 

The verification uses laravels validation package (`illuminate/validation`).

## Installation
Install the package via composer
`composer require slashplus/identity-germany`

## Usage
To make use of the validation just create a new instance, passing the validation data 
and method. Depending on the validation method (i.e. `id_card`) the correct validator 
instance will be created.

i.e.
```php
// note: this example contains invalid data, but you get an idea
$validator = \Slashplus\IdentityGermany\AgeVerification::create([
    [
    'serial' => '5484197598', 
    'birth' => '9512210', 
    'expire' => '4702025', 
    'nationality' => 'D', 
    'checksum' => '4'
    ]
]);
```

You are then able to use all validation methods from the `Slashplus\IdentityGermany\Contracts\Validation` 
contract, which extends the `\Illuminate\Contracts\Validation\Validator` contract.

This means you're able to get information from the validator if needed:

```php
// regular illuminate methods i.e.
$validatedData = $validator->validated(); // array of validated Data (throws exception)

// additional data i.e.
$validatedBirth = $validator->validatedBirthDate(); // DateTime object (throws exception)
$validatedExpire = $validator->validatedExpireDate(); // DateTime object (throws exception)
```

### Data structure
The input data might depend on the validation method. For information about it, 
check the methods directory and its readme:

- `id_card`: [src/Validation/IdCardValidation/README.md](/src/Validation/IdCardValidation/README.md)

## Roadmap
Currently there is only one method for checking against the german id card.
In a further release the plan is to extended the functionality by adding 
- a passport method 
- a german residence authorization method
