<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server\RequestHandler;

use Dogado\JsonApi\Exception\JsonApi\NotAllowedException;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Model\Response\ResponseInterface;

trait NoResourceFetchingTrait
{
    /**
     * @throws NotAllowedException
     */
    public function fetchResource(RequestInterface $request): ResponseInterface
    {
        throw new NotAllowedException('You are not allowed to fetch a resource of type ' . $request->type());
    }

    /**
     * @throws NotAllowedException
     */
    public function fetchResources(RequestInterface $request): ResponseInterface
    {
        throw new NotAllowedException('You are not allowed to fetch a resource of type ' . $request->type());
    }
}
