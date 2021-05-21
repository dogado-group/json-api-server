<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server\RequestHandler;

use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Model\Response\ResponseInterface;

interface RequestHandlerInterface
{
    public function fetchResource(RequestInterface $request): ResponseInterface;

    public function fetchResources(RequestInterface $request): ResponseInterface;

    public function fetchRelationship(RequestInterface $request): ResponseInterface;

    public function createResource(RequestInterface $request): ResponseInterface;

    public function patchResource(RequestInterface $request): ResponseInterface;

    public function deleteResource(RequestInterface $request): ResponseInterface;

    public function addRelatedResources(RequestInterface $request): ResponseInterface;

    public function replaceRelatedResources(RequestInterface $request): ResponseInterface;

    public function removeRelatedResources(RequestInterface $request): ResponseInterface;
}
