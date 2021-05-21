<?php

declare(strict_types=1);

namespace Dogado\JsonApi\Server\RequestHandler;

use Dogado\JsonApi\Exception\JsonApi\NotAllowedException;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Model\Response\ResponseInterface;

trait NoResourceDeletionTrait
{
    /**
     * @throws NotAllowedException
     */
    public function deleteResource(RequestInterface $request): ResponseInterface
    {
        throw new NotAllowedException('You are not allowed to delete a resource of type ' . $request->type());
    }
}
