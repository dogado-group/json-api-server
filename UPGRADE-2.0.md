# UPGRADE to v2.0.0

## Migrating JSON API model definitions from Doctrine annotations to php 8 attributes

In `v2.0.0` the support for JSON API model definitions via Doctrine annotations has been removed and moved to native
php 8 attributes. The annotation classes have been moved from the namespace `Dogado\JsonApi\Annotations` to
`Dogado\JsonApi\Attribute`. The attributes are basically the same. The only change is that the `value` attribute
in the `Type` and `Attribute` classes has been renamed to `name`.

Your models have to be changed like this:
```php
// OLD
use Dogado\JsonApi\Annotations\Attribute;
use Dogado\JsonApi\Annotations\Id;
use Dogado\JsonApi\Annotations\Type;

/**
 * @Type("dummy-model")
 */
class Model
{
    /**
     * @Id()
     */
    private ?int $id = 123456;

    /**
     * @Attribute(value="name-alias", ignoreOnNull=true)
     */
    private ?string $name = 'loremIpsum';
}
```
```php
// NEW
use Dogado\JsonApi\Attribute\Attribute;
use Dogado\JsonApi\Attribute\Id;
use Dogado\JsonApi\Attribute\Type;

#[Type('dummy-model')]
class Model
{
    #[Id]
    private ?int $id = 123456;

    #[Attribute(name: 'name-alias', ignoreOnNull: true)]
    private ?string $name = 'loremIpsum';
}
```

## Migrate to attribute constraint based Symfony model validation

Symfony added php 8 attribute constraint based validation in
[`symfony/validator:v5.2.0`](https://symfony.com/blog/new-in-symfony-5-2-constraints-as-php-attributes). It works just
like with Doctrine annotations, with one exception: The following validation constraints are not totally compatible to
php 8 attributes:

* All
* AtLeastOneOf
* Collection
* Compound (abstract)
* Existence (abstract)
** Required
** Optional
* Sequentially

[They are missing nested attributes](https://github.com/symfony/symfony/issues/38503) which is a feature that will
probably be added in php 8.1.

Although this package suggests using php 8 attribute constraints with release `v2.0.0`, feel free to continue to
use doctrine based annotations for validation. Symfony will continue to support them in Symfony 6.

Either way, you should pay attention to the way how you initialize the Symfony Validator.

### Validator initialization for php 8 attributes

```php
use Symfony\Component\Validator\ValidatorBuilder;

$validatorBuilder = new ValidatorBuilder();

// Symfony ^5.2
$validator = $validatorBuilder->enableAnnotationMapping(true)->getValidator();

// Symfony ^6.0
$validator = $validatorBuilder->enableAnnotationMapping()->getValidator();
```

php 8 validation constraints in combination with the new json api attribute definitions are a real benefit. Not only
is it faster, since php 8 attributes are a native php implementation, but you also do no longer need extra Doctrine
packages that analyze phpdocs. Here is an example how good both go together:

```php
use Dogado\JsonApi\Attribute\Attribute;
use Dogado\JsonApi\Attribute\Id;
use Dogado\JsonApi\Attribute\Type;
use Symfony\Component\Validator\Constraints as Assert;

#[Type('user')]
class User
{
    #[
        Id,
        Assert\Positive
    ]
    private ?int $id = 123456;

    #[
        Attribute,
        Assert\Length(min: 3, max: 32)
    ]
    private ?string $name = 'John Doe';

    #[
        Attribute,
        Assert\Email
    ]
    private ?string $email = null;
}
```

### Validator initialization for Doctrine annotations

The following change is not really necessary for `symfony/validator:^5.0`, but that change is the syntax that will still
be working in `symfony/validator:^6.0` and is already supported in `^5.2`.

```php
use Symfony\Component\Validator\ValidatorBuilder;

$validatorBuilder = new ValidatorBuilder();

// OLD
$validator = $validatorBuilder->enableAnnotationMapping()->getValidator();

// NEW
$validator = $validatorBuilder->enableAnnotationMapping()->addDefaultDoctrineAnnotationReader()->getValidator();
```