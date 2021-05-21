<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server\Validator;

use Dogado\JsonApi\Model\Error\Error;
use Dogado\JsonApi\Model\Error\ErrorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

class ViolationConverter implements ViolationConverterInterface
{
    public function toError(ConstraintViolationInterface $violation): ErrorInterface
    {
        return new Error(422, (string) $violation->getMessage());
    }
}
