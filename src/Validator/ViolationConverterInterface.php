<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server\Validator;

use Dogado\JsonApi\Model\Error\ErrorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

interface ViolationConverterInterface
{
    public function toError(ConstraintViolationInterface $violation): ErrorInterface;
}
