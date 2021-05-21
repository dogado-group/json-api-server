<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server\Exception;

use Dogado\JsonApi\Exception\JsonApiException;
use Dogado\JsonApi\Model\Error\Error;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Support\Error\ErrorCollection;
use Throwable;

class RequestValidationException extends JsonApiException
{
    public const CODE_DOCUMENT_EMPTY = 100;
    public const CODE_DOCUMENT_NOT_EMPTY = 101;
    public const CODE_RESOURCE_MISSING = 102;
    public const CODE_TYPE_MISMATCH = 103;
    public const CODE_RESOURCE_ID_EMPTY = 104;
    public const CODE_SCALAR_PAYLOAD_EXPECTED = 105;
    public const CODE_SCALAR_PAYLOAD_CONTAINS_ID = 106;

    protected int $httpStatus = 400;
    protected RequestInterface $request;

    public static function documentEmpty(RequestInterface $request): self
    {
        return new self(
            'The request document is empty, although a document was expected.',
            self::CODE_DOCUMENT_EMPTY,
            $request
        );
    }

    public static function documentNotEmpty(RequestInterface $request): self
    {
        return new self(
            'The request document is not empty, although it was expected.',
            self::CODE_DOCUMENT_NOT_EMPTY,
            $request
        );
    }

    public static function resourceMissing(RequestInterface $request): self
    {
        return new self(
            'The request document contains no JSON API resources, at least one expected.',
            self::CODE_RESOURCE_MISSING,
            $request
        );
    }

    public static function typeMismatch(
        RequestInterface $request,
        string $expected,
        string $actual,
        int $resourceItem
    ): self {
        return new self(
            sprintf(
                'JSON API request contains resources with unexpected type: "%s" expected, got "%s" for resource #%d',
                $expected,
                $actual,
                $resourceItem
            ),
            self::CODE_TYPE_MISMATCH,
            $request
        );
    }

    public static function resourceIdEmpty(
        RequestInterface $request,
        int $resourceItem
    ): self {
        return new self(
            sprintf(
                'JSON API request contains resources with empty IDs (resource #%d)',
                $resourceItem
            ),
            self::CODE_RESOURCE_ID_EMPTY,
            $request
        );
    }

    public static function scalarPayloadExpected(RequestInterface $request, int $totalResources): self
    {
        return new self(
            sprintf(
                'JSON API request contains %s, one expected',
                0 === $totalResources ? 'no resource' : $totalResources . ' resources'
            ),
            self::CODE_SCALAR_PAYLOAD_EXPECTED,
            $request
        );
    }

    public static function scalarPayloadContainsId(RequestInterface $request): self
    {
        return new self(
            'JSON API request contains a resource with id, although it\'s id must be null or not set',
            self::CODE_SCALAR_PAYLOAD_CONTAINS_ID,
            $request
        );
    }

    public function __construct(
        string $message,
        int $code,
        RequestInterface $request,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous, new ErrorCollection([new Error(
            $this->httpStatus,
            $message
        )]));
        $this->request = $request;
    }
}
