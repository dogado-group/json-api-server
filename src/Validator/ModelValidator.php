<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server\Validator;

use Dogado\JsonApi\Exception\JsonApi\ValidationException;
use Dogado\JsonApi\Support\Error\ErrorCollection;
use Dogado\JsonApi\Support\Model\DataModelAnalyser;
use ReflectionException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ModelValidator
{
    protected ViolationConverterInterface $violationConverter;

    public function __construct(
        protected ValidatorInterface $validator,
        ViolationConverterInterface $violationConverter = null
    ) {
        $this->violationConverter = $violationConverter ?? new ViolationConverter();
    }

    /**
     * @param Constraint|Constraint[]|null $constraints The constraint(s) to validate against
     * @param string|GroupSequence|string[]|GroupSequence[]|null $groups
     * @param string|null $documentPath The path to the resource within the JSON API document
     * @throws ValidationException
     * @throws ReflectionException
     */
    public function validate(
        object $model,
        mixed $constraints = null,
        mixed $groups = null,
        string $documentPath = null
    ): void {
        $documentPath ??= '/data';
        /** @var ConstraintViolationListInterface|ConstraintViolationInterface[] $violationList */
        $violationList = $this->validator->validate($model, $constraints, $groups);
        if (0 === $violationList->count()) {
            return;
        }

        $errors = new ErrorCollection();
        $analyser = DataModelAnalyser::process($model);

        $propertyMap = [];
        if (null !== $analyser->getIdPropertyName()) {
            $propertyMap[$analyser->getIdPropertyName()] = '/id';
        }

        foreach ($analyser->getAttributesPropertyMap() as $jsonApiAddress => $modelAddress) {
            $propertyMap[str_replace('/', '.', (string) $modelAddress)] = '/attributes/' . $jsonApiAddress;
        }

        foreach ($violationList as $violation) {
            $error = $this->violationConverter->toError($violation);
            if (isset($propertyMap[$violation->getPropertyPath()])) {
                $error->source()->set('pointer', $documentPath . $propertyMap[$violation->getPropertyPath()]);
            }

            $errors->add($error);
        }

        throw new ValidationException(
            'resource validation failed for "' . $analyser->getType() . '"',
            0,
            null,
            $errors
        );
    }
}
