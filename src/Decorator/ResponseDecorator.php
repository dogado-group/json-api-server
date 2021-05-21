<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server\Decorator;

use Dogado\JsonApi\Model\Document\DocumentInterface;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Model\Resource\ResourceInterface;
use Dogado\JsonApi\Model\Response\ResponseInterface;

class ResponseDecorator
{
    public function handle(RequestInterface $request, ResponseInterface $response): void
    {
        if (null === $response->document()) {
            return;
        }

        $document = $response->document();
        foreach ($document->data()->all() as $resource) {
            $this->includeRelated($document, $resource, $request);
            $this->cleanUpResource($resource, $request);
        }
    }

    protected function includeRelated(
        DocumentInterface $document,
        ResourceInterface $resource,
        RequestInterface $request
    ): void {
        foreach ($resource->relationships()->all() as $relationship) {
            $shouldIncludeRelationship = $request->requestsInclude($relationship->name());
            $subRequest = $request->createSubRequest($relationship->name(), $resource);
            foreach ($relationship->related()->all() as $related) {
                if (null === $related->id()) {
                    continue;
                }

                if ($shouldIncludeRelationship) {
                    $document->included()->merge($related);
                    $this->cleanUpResource($document->included()->get($related->type(), $related->id()), $subRequest);
                }
                $this->includeRelated($document, $related, $subRequest);
            }
        }
    }

    protected function cleanUpResource(ResourceInterface $resource, RequestInterface $request): void
    {
        foreach ($resource->attributes()->all() as $key => $value) {
            if (!$request->requestsAttributes() || !$request->requestsField($resource->type(), $key)) {
                $resource->attributes()->remove($key);
            }
        }

        if (!$request->requestsRelationships()) {
            foreach ($resource->relationships()->all() as $relationship) {
                $resource->relationships()->removeElement($relationship);
            }
        }
    }
}
