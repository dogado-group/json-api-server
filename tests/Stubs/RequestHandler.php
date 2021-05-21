<?php
namespace Dogado\JsonApi\Server\Tests\Stubs;

use Dogado\JsonApi\Server\RequestHandler\NoRelationshipFetchingTrait;
use Dogado\JsonApi\Server\RequestHandler\NoRelationshipModificationTrait;
use Dogado\JsonApi\Server\RequestHandler\NoResourceDeletionTrait;
use Dogado\JsonApi\Server\RequestHandler\NoResourceFetchingTrait;
use Dogado\JsonApi\Server\RequestHandler\NoResourceModificationTrait;
use Dogado\JsonApi\Server\RequestHandler\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{
    use NoRelationshipFetchingTrait;
    use NoRelationshipModificationTrait;
    use NoResourceDeletionTrait;
    use NoResourceFetchingTrait;
    use NoResourceModificationTrait;
}