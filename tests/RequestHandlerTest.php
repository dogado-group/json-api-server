<?php
namespace Dogado\JsonApi\Server\Tests;

use Dogado\JsonApi\Exception\JsonApi\NotAllowedException;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Server\Tests\Stubs\RequestHandler;

class RequestHandlerTest extends TestCase
{
    /**
     * @throws NotAllowedException
     */
    public function testFetchRelationship(): void
    {
        $requestHandler = new RequestHandler();
        $relationship = $this->faker->userName;
        $request = $this->createMock(RequestInterface::class);
        $request->expects(self::once())->method('relationship')->willReturn($relationship);

        $this->expectExceptionObject(
            new NotAllowedException("You are not allowed to fetch the relationship $relationship")
        );
        $requestHandler->fetchRelationship($request);
    }

    /**
     * @throws NotAllowedException
     */
    public function testAddRelatedResources(): void
    {
        $requestHandler = new RequestHandler();
        $relationship = $this->faker->userName;
        $request = $this->createMock(RequestInterface::class);
        $request->expects(self::once())->method('relationship')->willReturn($relationship);

        $this->expectExceptionObject(
            new NotAllowedException("You are not allowed to modify the relationship $relationship")
        );
        $requestHandler->addRelatedResources($request);
    }

    /**
     * @throws NotAllowedException
     */
    public function testReplaceRelatedResources(): void
    {
        $requestHandler = new RequestHandler();
        $relationship = $this->faker->userName;
        $request = $this->createMock(RequestInterface::class);
        $request->expects(self::once())->method('relationship')->willReturn($relationship);

        $this->expectExceptionObject(
            new NotAllowedException("You are not allowed to modify the relationship $relationship")
        );
        $requestHandler->replaceRelatedResources($request);
    }

    /**
     * @throws NotAllowedException
     */
    public function testRemoveRelatedResources(): void
    {
        $requestHandler = new RequestHandler();
        $relationship = $this->faker->userName;
        $request = $this->createMock(RequestInterface::class);
        $request->expects(self::once())->method('relationship')->willReturn($relationship);

        $this->expectExceptionObject(
            new NotAllowedException("You are not allowed to modify the relationship $relationship")
        );
        $requestHandler->removeRelatedResources($request);
    }

    /**
     * @throws NotAllowedException
     */
    public function testDeleteResource(): void
    {
        $requestHandler = new RequestHandler();
        $type = $this->faker->userName;
        $request = $this->createMock(RequestInterface::class);
        $request->expects(self::once())->method('type')->willReturn($type);

        $this->expectExceptionObject(
            new NotAllowedException("You are not allowed to delete a resource of type $type")
        );
        $requestHandler->deleteResource($request);
    }

    /**
     * @throws NotAllowedException
     */
    public function testFetchResource(): void
    {
        $requestHandler = new RequestHandler();
        $type = $this->faker->userName;
        $request = $this->createMock(RequestInterface::class);
        $request->expects(self::once())->method('type')->willReturn($type);

        $this->expectExceptionObject(
            new NotAllowedException("You are not allowed to fetch a resource of type $type")
        );
        $requestHandler->fetchResource($request);
    }

    /**
     * @throws NotAllowedException
     */
    public function testFetchResources(): void
    {
        $requestHandler = new RequestHandler();
        $type = $this->faker->userName;
        $request = $this->createMock(RequestInterface::class);
        $request->expects(self::once())->method('type')->willReturn($type);

        $this->expectExceptionObject(
            new NotAllowedException("You are not allowed to fetch a resource of type $type")
        );
        $requestHandler->fetchResources($request);
    }

    /**
     * @throws NotAllowedException
     */
    public function testCreateResource(): void
    {
        $requestHandler = new RequestHandler();
        $type = $this->faker->userName;
        $request = $this->createMock(RequestInterface::class);
        $request->expects(self::once())->method('type')->willReturn($type);

        $this->expectExceptionObject(
            new NotAllowedException("You are not allowed to create a resource of type $type")
        );
        $requestHandler->createResource($request);
    }

    /**
     * @throws NotAllowedException
     */
    public function testPatchResource(): void
    {
        $requestHandler = new RequestHandler();
        $type = $this->faker->userName;
        $request = $this->createMock(RequestInterface::class);
        $request->expects(self::once())->method('type')->willReturn($type);

        $this->expectExceptionObject(
            new NotAllowedException("You are not allowed to modify a resource of type $type")
        );
        $requestHandler->patchResource($request);
    }
}