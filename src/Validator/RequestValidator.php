<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server\Validator;

use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Server\Exception\RequestValidationException;

class RequestValidator
{
    /**
     * @throws RequestValidationException
     */
    public function assertDocument(RequestInterface $request): void
    {
        if (null === $request->document()) {
            throw RequestValidationException::documentEmpty($request);
        }
    }

    /**
     * @throws RequestValidationException
     */
    public function assertNoDocument(RequestInterface $request): void
    {
        if (null !== $request->document()) {
            throw RequestValidationException::documentNotEmpty($request);
        }
    }

    /**
     * @throws RequestValidationException
     */
    public function assertDataNotEmpty(RequestInterface $request): void
    {
        $this->assertDocument($request);
        assert($request->document() instanceof DocumentInterface);
        if ($request->document()->data()->isEmpty()) {
            throw RequestValidationException::resourceMissing($request);
        }
    }

    /**
     * @throws RequestValidationException
     */
    public function assertResourcesMatchType(RequestInterface $request, string $type): void
    {
        $this->assertDocument($request);
        assert($request->document() instanceof DocumentInterface);
        foreach ($request->document()->data()->all() as $key => $resource) {
            if ($type !== $resource->type()) {
                throw RequestValidationException::typeMismatch($request, $type, $resource->type(), $key);
            }
        }
    }

    /**
     * @throws RequestValidationException
     */
    public function assertResourcesMatchTypeAndContainIds(RequestInterface $request, string $type): void
    {
        $this->assertResourcesMatchType($request, $type);
        assert($request->document() instanceof DocumentInterface);
        foreach ($request->document()->data()->all() as $key => $resource) {
            if (empty($resource->id())) {
                throw RequestValidationException::resourceIdEmpty($request, $key);
            }
        }
    }

    /**
     * @throws RequestValidationException
     */
    public function assertScalarResultWithId(RequestInterface $request, string $type): void
    {
        $this->assertDataNotEmpty($request);
        $this->assertResourcesMatchTypeAndContainIds($request, $type);
        assert($request->document() instanceof DocumentInterface);
        if (1 !== $request->document()->data()->count()) {
            throw RequestValidationException::scalarPayloadExpected($request, $request->document()->data()->count());
        }
    }

    /**
     * @throws RequestValidationException
     */
    public function assertScalarResultWithoutId(RequestInterface $request, string $type): void
    {
        $this->assertDataNotEmpty($request);
        $this->assertResourcesMatchType($request, $type);
        assert($request->document() instanceof DocumentInterface);

        $resource = $request->document()->data()->first();
        assert($resource instanceof ResourceInterface);
        if (null !== $resource->id()) {
            throw RequestValidationException::scalarPayloadContainsId($request);
        }
    }
}
