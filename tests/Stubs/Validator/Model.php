<?php
declare(strict_types=1);

namespace Dogado\JsonApi\Server\Tests\Stubs\Validator;

use Dogado\JsonApi\Attribute\Attribute;
use Dogado\JsonApi\Attribute\Type;
use Symfony\Component\Validator\Constraints as Assert;

#[Type('validation-test')]
class Model
{
    #[
        Attribute('jsonApiValueObject'),
        Assert\Valid
    ]
    private ValueObjectModel $valueObject;

    public function __construct()
    {
        $this->valueObject = new ValueObjectModel();
    }
}