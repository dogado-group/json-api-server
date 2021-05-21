<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server\RequestHandler;

use Dogado\JsonApi\Exception\JsonApi\NotAllowedException;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Model\Response\ResponseInterface;

trait NoRelationshipFetchingTrait
{
    /**
     * @throws NotAllowedException
     */
    public function fetchRelationship(RequestInterface $request): ResponseInterface
    {
        throw new NotAllowedException(
            'You are not allowed to fetch the relationship ' . ($request->relationship() ?? 'unknown')
        );
    }
}
