<?php

namespace Dogado\JsonApi\Server\Tests\Validator;

use Dogado\JsonApi\Exception\JsonApiException;
use Dogado\JsonApi\Model\Error\ErrorInterface;
use Dogado\JsonApi\Server\Tests\Stubs\Validator\Model;
use Dogado\JsonApi\Server\Tests\TestCase;
use Dogado\JsonApi\Server\Validator\ModelValidator;
use Symfony\Component\Validator\ValidatorBuilder;

class ModelValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $model = new Model();
        $validatorBuilder = new ValidatorBuilder();
        $validator = $validatorBuilder->enableAnnotationMapping()->getValidator();

        $jsonApiValidator = new ModelValidator($validator);
        try {
            $jsonApiValidator->validate($model);
        } catch (JsonApiException $e) {
            $this->assertStringContainsString('resource validation failed', $e->getMessage());
            /** @var ErrorInterface $error */
            $error = $e->errors()->first();
            $this->assertInstanceOf(ErrorInterface::class, $error);
            $this->assertEquals(422, $e->getHttpStatus());
            $this->assertEquals('This value is not a valid email address.', $error->title());
            $this->assertEquals('/data/attributes/jsonApiValueObject/jsonApiEmail', $error->source()->get('pointer'));
        }
    }

    public function testValidateWithCustomDocumentPath(): void
    {
        $model = new Model();
        $validatorBuilder = new ValidatorBuilder();
        $validator = $validatorBuilder->enableAnnotationMapping()->getValidator();

        $customPath = '/' . $this->faker()->word() . '/' . $this->faker()->word();
        $jsonApiValidator = new ModelValidator($validator);
        try {
            $jsonApiValidator->validate($model, null, null, $customPath);
        } catch (JsonApiException $e) {
            /** @var ErrorInterface $error */
            $error = $e->errors()->first();
            $this->assertInstanceOf(ErrorInterface::class, $error);
            $this->assertEquals(
                $customPath . '/attributes/jsonApiValueObject/jsonApiEmail',
                $error->source()->get('pointer')
            );
        }
    }
}
