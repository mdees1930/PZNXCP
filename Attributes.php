<?php

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)] //menentukan target atribut
class NotBlank
{

}


class Length
{
    public int $min;
    public int $max;

    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }
}

class LoginRequest
{
    #[NotBlank]
    #[Length(min: 4, max: 10)]
    public ?string $username;

    #[NotBlank]
    #[Length(min: 8, max: 10)]
    public ?string $password;
}

function validate (object $object): void
{
    $class = new ReflectionClass($object);
    $properties = $class->getProperties();
    foreach ($properties as $property){
        validateNotBlank($property, $object);
        validateLength($property, $object);
    }
}



function validateNotBlank(ReflectionProperty $property, Object $object): void
{
    $attributes = $property->getAttributes(NotBlank::class);
    if(count($attributes) > 0){
        if(!$property->isInitialized($object))
            throw new Exception("Property $property->name is Null");
        if($property->getValue($object)==null)
            throw new Exception("Property $property->name is Null");  
    }
}

function validateLength(ReflectionProperty $property, Object $object): void
{
    if (!$property->isInitialized($object) || $property->getValue($object) == null) {
        return;//cancel validate, karena sudah ada di function validateNotBlank
    }

    $value = $property->getValue ($object);
    $attributes = $property->getAttributes(Length::class);
    foreach ($attributes as $attribute) {
        $Length = $attribute->newInstance();//membuat object length sesuai deklarasi
        $valueLength = strlen($value);
        if($valueLength < $Length->min)
            throw new Exception("Property $property->name is too short");
        if($valueLength > $Length->max)
            throw new Exception("Property $property->name is too long");        


    }
}

$request = new LoginRequest();
$request->username = "Mds";
$request->password = "Rahasia";
validate($request);