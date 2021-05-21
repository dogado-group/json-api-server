<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server\RequestHandler;

use Dogado\JsonApi\Exception\JsonApi\NotAllowedException;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Model\Response\ResponseInterface;

trait NoResourceModificationTrait
{
    /**
     * @throws NotAllowedException
     */
    public function createResource(RequestInterface $request): ResponseInterface
    {
        throw new NotAllowedException('You are not allowed to create a resource of type ' . $request->type());
    }

    /**
     * @throws NotAllowedException
     */
    public function patchResource(RequestInterface $request): ResponseInterface
    {
        throw new NotAllowedException('You are not allowed to modify a resource of type ' . $request->type());
    }
}
