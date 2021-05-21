<?php

namespace Dogado\JsonApi\Server\Tests\Validator;

use Dogado\JsonApi\Exception\JsonApi\BadRequestException;
use Dogado\JsonApi\Model\Document\Document;
use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Model\Request\Request;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Model\Resource\Resource;
use Dogado\JsonApi\Server\Exception\RequestValidationException;
use Dogado\JsonApi\Server\Tests\TestCase;
use Dogado\JsonApi\Server\Validator\RequestValidator;
use GuzzleHttp\Psr7\Uri;

class RequestValidatorTest extends TestCase
{
    private RequestValidator $requestValidator;

    protected function setUp(): void
    {
        $this->requestValidator = new RequestValidator();
    }

    public function testAssertDocument(): void
    {
        $this->requestValidator->assertDocument($this->createRequest(new Document([])));
        $this->assertTrue(true);
    }

    public function testAssertDocumentThrowsException(): void
    {
        $request = $this->createRequest();
        $this->expectExceptionObject(RequestValidationException::documentEmpty($request));

        $this->requestValidator->assertDocument($request);
    }

    public function testAssertNoDocument(): void
    {
        $this->requestValidator->assertNoDocument($this->createRequest());
        $this->assertTrue(true);
    }

    public function testAssertNoDocumentThrowsException(): void
    {
        $request = $this->createRequest(new Document([]));
        $this->expectExceptionObject(RequestValidationException::documentNotEmpty($request));

        $this->requestValidator->assertNoDocument($request);
    }

    public function testAssertDataNotEmpty(): void
    {
        $this->requestValidator->assertDataNotEmpty($this->createRequest(new Document([new Resource(
            $this->faker()->slug
        )])));
        $this->assertTrue(true);
    }

    public function testAssertDataNotEmptyThrowsException(): void
    {
        $request = $this->createRequest(new Document([]));
        $this->expectExceptionObject(RequestValidationException::resourceMissing($request));

        $this->requestValidator->assertDataNotEmpty($request);
    }

    public function testAssertResourcesMatchType(): void
    {
        $type = $this->faker()->slug;
        $request = $this->createRequest(new Document([new Resource(
            $type
        )]));
        $this->requestValidator->assertResourcesMatchType($request, $type);
        $this->assertTrue(true);
    }

    public function testAssertResourcesMatchTypeThrowsException(): void
    {
        $type = $this->faker()->slug;
        $resource = new Resource(
            $this->faker()->slug
        );
        $request = $this->createRequest(new Document([$resource]));
        $this->expectExceptionObject(RequestValidationException::typeMismatch($request, $type, $resource->type(), 0));
        $this->requestValidator->assertResourcesMatchType($request, $type);
    }

    public function testAssertResourcesMatchTypeAndContainIds(): void
    {
        $type = $this->faker()->slug;
        $request = $this->createRequest(new Document([new Resource(
            $type,
            (string) $this->faker()->numberBetween()
        )]));
        $this->requestValidator->assertResourcesMatchTypeAndContainIds($request, $type);
        $this->assertTrue(true);
    }

    public function testAssertResourcesMatchTypeAndContainIdsThrowsExceptionDueToType(): void
    {
        $expectedType = $this->faker()->slug;
        $actualType = $this->faker()->slug;
        $request = $this->createRequest(new Document([new Resource(
            $actualType,
            (string) $this->faker()->numberBetween()
        )]));
        $this->expectExceptionObject(
            RequestValidationException::typeMismatch($request, $expectedType, $actualType, 0)
        );
        $this->requestValidator->assertResourcesMatchTypeAndContainIds($request, $expectedType);
    }

    public function testAssertResourcesMatchTypeAndContainIdsThrowsExceptionDueToId(): void
    {
        $expectedType = $this->faker()->slug;
        $request = $this->createRequest(new Document([new Resource(
            $expectedType
        )]));
        $this->expectExceptionObject(
            RequestValidationException::resourceIdEmpty($request, 0)
        );
        $this->requestValidator->assertResourcesMatchTypeAndContainIds($request, $expectedType);
    }

    public function testAssertScalarResultWithId(): void
    {
        $type = $this->faker()->slug;
        $request = $this->createRequest(new Document([new Resource(
            $type,
            (string) $this->faker()->numberBetween()
        )]));
        $this->requestValidator->assertScalarResultWithId($request, $type);
        $this->assertTrue(true);
    }

    public function testAssertScalarResultWithIdThrowsException(): void
    {
        $type = $this->faker()->slug;
        $request = $this->createRequest(new Document([
            new Resource($type, (string) $this->faker()->numberBetween()),
            new Resource($type, (string) $this->faker()->numberBetween()),
        ]));

        $this->expectExceptionObject(
            RequestValidationException::scalarPayloadExpected($request, 2)
        );
        $this->requestValidator->assertScalarResultWithId($request, $type);
    }

    public function testAssertScalarResultWithoutId(): void
    {
        $type = $this->faker()->slug;
        $request = $this->createRequest(new Document([new Resource(
            $type
        )]));
        $this->requestValidator->assertScalarResultWithoutId($request, $type);
        $this->assertTrue(true);
    }

    public function testAssertScalarResultWithoutIdThrowsException(): void
    {
        $type = $this->faker()->slug;
        $request = $this->createRequest(new Document([
             new Resource($type, (string) $this->faker()->numberBetween()),
         ]));

        $this->expectExceptionObject(
            RequestValidationException::scalarPayloadContainsId($request)
        );
        $this->requestValidator->assertScalarResultWithoutId($request, $type);
    }

    /**
     * @throws BadRequestException
     */
    private function createRequest(?DocumentInterface $document = null): RequestInterface
    {
        $method = $this->faker()->randomElement(['GET', 'POST', 'PATCH', 'DELETE']);
        $uri = new Uri('https://localhost/test');
        return new Request($method, $uri, $document);
    }
}
