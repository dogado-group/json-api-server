<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server;

use Dogado\JsonApi\Exception\DocumentSerializerException;
use Dogado\JsonApi\Exception\JsonApi\BadRequestException;
use Dogado\JsonApi\Exception\JsonApi\UnsupportedMediaTypeException;
use Dogado\JsonApi\Exception\JsonApi\UnsupportedTypeException;
use Dogado\JsonApi\JsonApiTrait;
use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Model\Error\Error;
use Dogado\JsonApi\Model\JsonApiInterface;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Model\Response\DocumentResponse;
use Dogado\JsonApi\Model\Response\ResponseInterface;
use Dogado\JsonApi\Serializer\Deserializer;
use Dogado\JsonApi\Serializer\DocumentDeserializerInterface;
use Dogado\JsonApi\Serializer\DocumentSerializerInterface;
use Dogado\JsonApi\Serializer\Serializer;
use Dogado\JsonApi\Server\Decorator\ResponseDecorator;
use Dogado\JsonApi\Server\RequestHandler\RequestHandlerInterface;
use Throwable;

class JsonApiServer
{
    use JsonApiTrait;

    protected DocumentDeserializerInterface $deserializer;
    protected DocumentSerializerInterface $serializer;
    protected ResponseDecorator $responseDecorator;

    /** @var RequestHandlerInterface[] */
    protected array $handlers = [];

    public function __construct(
        ?DocumentDeserializerInterface $deserializer = null,
        ?DocumentSerializerInterface $serializer = null,
        ?ResponseDecorator $responseDecorator = null
    ) {
        $this->deserializer = $deserializer ?? new Deserializer();
        $this->serializer = $serializer ?? new Serializer();
        $this->responseDecorator = $responseDecorator ?? new ResponseDecorator();
    }

    public function addHandler(string $type, RequestHandlerInterface $handler): self
    {
        $this->handlers[$type] = $handler;
        return $this;
    }

    /**
     * @return RequestHandlerInterface
     * @throws UnsupportedTypeException
     */
    private function getHandler(string $type): RequestHandlerInterface
    {
        if (!array_key_exists($type, $this->handlers)) {
            throw new UnsupportedTypeException($type);
        }

        return $this->handlers[$type];
    }

    /**
     * @throws BadRequestException
     */
    public function createRequestBody(?string $requestBody): ?DocumentInterface
    {
        if (empty($requestBody)) {
            return null;
        }

        $documentData = json_decode($requestBody, true);
        if (!is_array($documentData)) {
            throw new BadRequestException(
                'The request body is no valid json document: ' . json_last_error_msg()
            );
        }

        return $this->deserializer->deserializeDocument($documentData);
    }

    /**
     * @throws DocumentSerializerException
     */
    public function createResponseBody(ResponseInterface $response): string
    {
        if (null === $response->document()) {
            return '';
        }

        $responseData = json_encode($this->serializer->serializeDocument($response->document()));
        if (!is_string($responseData)) {
            throw DocumentSerializerException::unableGenerateJsonDocument(json_last_error_msg());
        }

        return $responseData;
    }

    public function handleException(Throwable $throwable, bool $debug = false): ResponseInterface
    {
        $apiError = Error::createFrom($throwable, $debug);

        $document = $this->singleResourceDocument();
        $document->errors()->add($apiError);

        return new DocumentResponse($document, null, $apiError->status());
    }

    /**
     * @throws BadRequestException
     * @throws UnsupportedTypeException
     * @throws UnsupportedMediaTypeException
     */
    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        if (JsonApiInterface::CONTENT_TYPE !== $request->headers()->get('Content-Type')) {
            throw new UnsupportedMediaTypeException($request->headers()->get('Content-Type') ?? '');
        }

        $handler = $this->getHandler($request->type());
        switch ($request->method()) {
            case 'GET':
                if ($request->id()) {
                    if ($request->relationship()) {
                        $response = $handler->fetchRelationship($request);
                        break;
                    }
                    $response = $handler->fetchResource($request);
                    break;
                }
                $response = $handler->fetchResources($request);
                break;
            case 'POST':
                if ($request->relationship()) {
                    $response = $handler->addRelatedResources($request);
                    break;
                }
                $response = $handler->createResource($request);
                break;
            case 'PATCH':
                if ($request->relationship()) {
                    $response = $handler->replaceRelatedResources($request);
                    break;
                }
                $response = $handler->patchResource($request);
                break;
            case 'DELETE':
                if ($request->relationship()) {
                    $response = $handler->removeRelatedResources($request);
                    break;
                }
                $response = $handler->deleteResource($request);
                break;
            default:
                throw new BadRequestException('Request method "' . $request->method() . '" is not supported');
        }

        $this->responseDecorator->handle($request, $response);
        return $response;
    }
}
