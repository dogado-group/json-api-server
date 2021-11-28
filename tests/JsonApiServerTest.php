<?php

namespace Dogado\JsonApi\Server\Tests;

use Dogado\JsonApi\Exception\DocumentSerializerException;
use Dogado\JsonApi\Exception\JsonApi\BadRequestException;
use Dogado\JsonApi\Exception\JsonApi\UnsupportedMediaTypeException;
use Dogado\JsonApi\Exception\JsonApi\UnsupportedTypeException;
use Dogado\JsonApi\Model\Document\Document;
use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Model\Error\Error;
use Dogado\JsonApi\Model\JsonApiInterface;
use Dogado\JsonApi\Model\Request\Request;
use Dogado\JsonApi\Model\Response\DocumentResponse;
use Dogado\JsonApi\Model\Response\ResponseInterface;
use Dogado\JsonApi\Serializer\DocumentDeserializerInterface;
use Dogado\JsonApi\Serializer\DocumentSerializerInterface;
use Dogado\JsonApi\Server\Decorator\ResponseDecorator;
use Dogado\JsonApi\Server\JsonApiServer;
use Dogado\JsonApi\Server\RequestHandler\RequestHandlerInterface;
use Dogado\JsonApi\Support\Collection\KeyValueCollection;
use Generator;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Throwable;

class JsonApiServerTest extends TestCase
{
    /** @var DocumentDeserializerInterface|MockObject */
    protected $deserializer;

    /** @var DocumentSerializerInterface|MockObject */
    protected  $serializer;

    /** @var ResponseDecorator|MockObject */
    protected $responseDecorator;

    /** @var RequestHandlerInterface|callable|MockObject */
    protected $requestHandler;

    protected JsonApiServer $server;

    protected function setUp(): void
    {
        $this->deserializer = $this->createMock(DocumentDeserializerInterface::class);
        $this->serializer = $this->createMock(DocumentSerializerInterface::class);
        $this->responseDecorator = $this->createMock(ResponseDecorator::class);
        $this->requestHandler = $this->createMock(RequestHandlerInterface::class);
        $this->server = new JsonApiServer(
            $this->deserializer,
            $this->serializer,
            $this->responseDecorator
        );
    }

    /** @dataProvider handlerAssociationScenarios */
    public function testHandlerAssociations(string $method, string $type, string $uri, string $handlerMethod): void
    {
        $this->server->addHandler($type, $this->requestHandler);

        $request = new Request($method, new Uri($uri));
        $response = $this->createMock(ResponseInterface::class);
        $this->requestHandler->expects(self::once())->method($handlerMethod)->with($request)
            ->willReturn($response);

        $this->responseDecorator->expects(self::once())->method('handle')->with($request, $response);
        $this->assertEquals($response, $this->server->handleRequest($request));
    }

    public function handlerAssociationScenarios(): Generator
    {
        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s/%s/relationships/%s',
            $this->faker()->domainName(),
            $type,
            $this->faker()->numberBetween(),
            $this->faker()->slug()
        );
        yield ['GET', $type, $uri, 'fetchRelationship'];

        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s/%s',
            $this->faker()->domainName(),
            $type,
            $this->faker()->numberBetween()
        );
        yield ['GET', $type, $uri, 'fetchResource'];

        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s',
            $this->faker()->domainName(),
            $type
        );
        yield ['GET', $type, $uri, 'fetchResources'];

        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s/%s/relationships/%s',
            $this->faker()->domainName(),
            $type,
            $this->faker()->numberBetween(),
            $this->faker()->slug()
        );
        yield ['POST', $type, $uri, 'addRelatedResources'];

        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s',
            $this->faker()->domainName(),
            $type
        );
        yield ['POST', $type, $uri, 'createResource'];

        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s/%s/relationships/%s',
            $this->faker()->domainName(),
            $type,
            $this->faker()->numberBetween(),
            $this->faker()->slug()
        );
        yield ['PATCH', $type, $uri, 'replaceRelatedResources'];

        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s/%s',
            $this->faker()->domainName(),
            $type,
            $this->faker()->numberBetween()
        );
        yield ['PATCH', $type, $uri, 'patchResource'];

        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s/%s/relationships/%s',
            $this->faker()->domainName(),
            $type,
            $this->faker()->numberBetween(),
            $this->faker()->slug()
        );
        yield ['DELETE', $type, $uri, 'removeRelatedResources'];

        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s/%s',
            $this->faker()->domainName(),
            $type,
            $this->faker()->numberBetween()
        );
        yield ['DELETE', $type, $uri, 'deleteResource'];
    }

    public function testCallableHandlerAssociation(): void
    {
        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s/%s',
            $this->faker()->domainName(),
            $type,
            $this->faker()->numberBetween()
        );

        $this->server->addHandler($type, fn () => $this->requestHandler);

        $request = new Request('GET', new Uri($uri));
        $response = $this->createMock(ResponseInterface::class);
        $this->requestHandler->expects(self::exactly(2))->method('fetchResource')->with($request)
            ->willReturn($response);

        $this->responseDecorator->expects(self::exactly(2))->method('handle')->with($request, $response);
        $this->assertEquals($response, $this->server->handleRequest($request));
        // execute a second time to test caching behaviour
        $this->assertEquals($response, $this->server->handleRequest($request));
    }

    public function testUnknownType(): void
    {
        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s/%s',
            $this->faker()->domainName(),
            $type,
            $this->faker()->numberBetween()
        );
        $request = new Request('GET', new Uri($uri));
        $this->expectExceptionObject(new UnsupportedTypeException($type));
        $this->server->handleRequest($request);
    }

    public function testInvalidCallableHandlerInitialization(): void
    {
        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s/%s',
            $this->faker()->domainName(),
            $type,
            $this->faker()->numberBetween()
        );
        $request = new Request('GET', new Uri($uri));
        $this->server->addHandler($type, fn () => $this->faker()->text());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Unable to initialize.*by given callable/');
        $this->server->handleRequest($request);
    }

    public function testInvalidContentType(): void
    {
        $type = $this->faker()->slug();
        $uri = sprintf(
            'http://%s/%s/%s',
            $this->faker()->domainName(),
            $type,
            $this->faker()->numberBetween()
        );
        $request = new Request('GET', new Uri($uri));
        $request->headers()->set('Content-Type', $this->faker()->slug());
        $this->expectExceptionObject(new UnsupportedMediaTypeException($request->headers()->get('Content-Type')));
        $this->server->handleRequest($request);
    }

    public function testInvalidHttpMethod(): void
    {
        $type = $this->faker()->slug();
        $this->server->addHandler($type, $this->requestHandler);

        $method = $this->faker()->userName();
        $request = $this->createMock(Request::class);

        $headers = new KeyValueCollection(['Content-Type' => JsonApiInterface::CONTENT_TYPE]);
        $request->expects(self::once())->method('headers')->willReturn($headers);
        $request->expects(self::atLeastOnce())->method('method')->willReturn($method);
        $request->expects(self::once())->method('type')->willReturn($type);

        $this->expectExceptionObject(new BadRequestException('Request method "' . $method . '" is not supported'));
        $this->server->handleRequest($request);
    }

    public function testCreateRequestBodyEmpty(): void
    {
        $this->assertEquals(null, $this->server->createRequestBody(null));
    }

    public function testCreateRequestBodyInvalidJson(): void
    {
        $this->expectException(BadRequestException::class);
        $this->server->createRequestBody($this->faker()->text());
    }

    public function testCreateRequestBody(): void
    {
        $data = [$this->faker()->text()];
        $jsonData = json_encode($data);
        $document = $this->createMock(DocumentInterface::class);
        $this->deserializer->expects(self::once())->method('deserializeDocument')->with($data)->willReturn($document);
        $this->assertEquals($document, $this->server->createRequestBody($jsonData));
    }

    public function testCreateResponseBodyEmpty(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::once())->method('document')->willReturn(null);
        $this->assertEquals('', $this->server->createResponseBody($response));
    }

    public function testCreateResponseBodyInvalidJson(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $invalidString = utf8_decode('ööäöä');

        $document = $this->createMock(DocumentInterface::class);
        $response->method('document')->willReturn($document);
        $this->serializer->method('serializeDocument')->with($document)->willReturn([$invalidString]);

        $this->expectException(DocumentSerializerException::class);
        $this->server->createResponseBody($response);
    }

    public function testCreateResponseBody(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $data = [$this->faker()->text()];

        $document = $this->createMock(DocumentInterface::class);
        $response->method('document')->willReturn($document);
        $this->serializer->method('serializeDocument')->with($document)->willReturn($data);

        $this->assertEquals(json_encode($data), $this->server->createResponseBody($response));
    }

    public function testHandleException(): void
    {
        $throwable = $this->createMock(Throwable::class);
        $debug = $this->faker()->boolean();
        $apiError = Error::createFrom($throwable, $debug);

        $document = new Document();
        $document->errors()->add($apiError);

        $expected = new DocumentResponse($document, null, $apiError->status());
        $this->assertEquals($expected, $this->server->handleException($throwable, $debug));
    }
}