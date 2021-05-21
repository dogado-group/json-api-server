[back to README](../README.md)

# Validator for data models

The `dogado/json-api-common` package allows the conversion from resource instances to data models. It's usually the case, that we need some sort of validation on the JSON API data that is coming in.
The `Dogado\JsonApi\Server\Validator\Validator` class offers the possibility to validate any model in it the current state, based on `symfony/validator`.

Usually, you can use any validation you like. Although this validator will throw a custom JSON API compatible `\Dogado\JsonApi\Exception\JsonApi\ValidationException` containing error objects representing the Symfony Validator violations.

## Code example

### Model class definition
```php
use Dogado\JsonApi\Annotations\Attribute;
use Dogado\JsonApi\Annotations\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Type("validation-test")
 */
class Model
{
    /**
     * @Attribute()
     * @Assert\Email()
     */
    private string $email = 'noValidEmail';

    /**
     * @Attribute("jsonApiValueObject")
     * @Assert\Valid()
     */
    private ValueObjectModel $valueObject;

    public function __construct()
    {
        $this->valueObject = new ValueObjectModel();
    }
}
```

It is also possible to validate value objects. To do that, you need to add the `@Assert\Valid()` annotation to the object property. Within the value object, you can continue to use the regular assertions.

### Execute validation

```php
use Dogado\JsonApi\Exception\JsonApiException;
use Dogado\JsonApi\Server\Tests\Stubs\Validator\Model;
use Dogado\JsonApi\Server\Validator\ModelValidator;
use Symfony\Component\Validator\ValidatorBuilder;

$model = new Model();
$validatorBuilder = new ValidatorBuilder();
$validator = $validatorBuilder->enableAnnotationMapping()->getValidator();

$jsonApiValidator = new ModelValidator($validator);
/** @throws JsonApiException */
$jsonApiValidator->validate($model);
```

In the above example we are using the annotation mapping that Symfony Validator offers by default. Feel free to use a different way of defining your validation rules. More details and validation examples can be found in the [Symfony documentation](https://symfony.com/doc/current/validation.html).

## Custom violation converter

The Symfony validator creates violations for each failing assertion rule. The JSON API server validator will send all violations against a `Dogado\JsonApi\Server\Validator\ViolationConverterInterface`. There already is a default `ViolationConverter` which will be used, but feel free to create a custom one and pass that one as parameter to the Validator instantiation.

*****

[prev: Exception handling](04-exception-handling.md) | [back to README](../README.md)
