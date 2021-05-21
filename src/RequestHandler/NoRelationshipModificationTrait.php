<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server\RequestHandler;

use Dogado\JsonApi\Exception\JsonApi\NotAllowedException;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Model\Response\ResponseInterface;

trait NoRelationshipModificationTrait
{
    /**
     * @throws NotAllowedException
     */
    public function addRelatedResources(RequestInterface $request): ResponseInterface
    {
        throw new NotAllowedException(
            'You are not allowed to modify the relationship ' . ($request->relationship() ?? 'unknown')
        );
    }

    /**
     * @throws NotAllowedException
     */
    public function replaceRelatedResources(RequestInterface $request): ResponseInterface
    {
        throw new NotAllowedException(
            'You are not allowed to modify the relationship ' . ($request->relationship() ?? 'unknown')
        );
    }

    /**
     * @throws NotAllowedException
     */
    public function removeRelatedResources(RequestInterface $request): ResponseInterface
    {
        throw new NotAllowedException(
            'You are not allowed to modify the relationship ' . ($request->relationship() ?? 'unknown')
        );
    }
}
