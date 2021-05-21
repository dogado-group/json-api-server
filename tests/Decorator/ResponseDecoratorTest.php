<?php
namespace Dogado\JsonApi\Server\Tests\Decorator;

use Dogado\JsonApi\Exception\JsonApi\BadRequestException;
use Dogado\JsonApi\Model\Document\Document;
use Dogado\JsonApi\Model\Request\Request;
use Dogado\JsonApi\Model\Request\RequestInterface;
use Dogado\JsonApi\Model\Resource\Relationship\Relationship;
use Dogado\JsonApi\Model\Resource\Resource;
use Dogado\JsonApi\Model\Response\ResponseInterface;
use Dogado\JsonApi\Server\Decorator\ResponseDecorator;
use Dogado\JsonApi\Server\Tests\TestCase;
use GuzzleHttp\Psr7\Uri;

class ResponseDecoratorTest extends TestCase
{
    public function testNoDocument(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::once())->method('document')->willReturn(null);
        (new ResponseDecorator())->handle($request, $response);
    }

    /**
     * @throws BadRequestException
     */
    public function testIncludeRelationship(): void
    {
        $relatedResource = new Resource($this->faker()->slug, (string) $this->faker()->numberBetween(), [
            'name' => $this->faker()->domainName,
        ]);
        $relationship = new Relationship($this->faker->slug, $relatedResource);

        $resource = new Resource($this->faker()->slug, (string) $this->faker->numberBetween());
        $resource->relationships()->set($relationship);
        $document = new Document($resource);

        $uri = sprintf(
            'http://%s/%s/%s/relationships/%s?include=%s',
            $this->faker()->domainName,
            $resource->type(),
            $resource->id(),
            $relationship->name(),
            $relationship->name().'.'.$resource->type()
        );
        $request = new Request('GET', new Uri($uri));

        $expected = clone $document;
        $expected->included()->merge($relatedResource);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('document')->willReturn($document);
        (new ResponseDecorator())->handle($request, $response);
        $this->assertEquals($expected, $document);
    }

    /**
     * @throws BadRequestException
     */
    public function testCleanupAttributes(): void
    {
        $resource = new Resource($this->faker()->slug, (string) $this->faker->numberBetween(), [
            'name' => $this->faker()->domainName,
        ]);
        $relationship = new Relationship($this->faker->slug);
        $resource->relationships()->set($relationship);
        $document = new Document($resource);

        $uri = sprintf(
            'http://%s/%s/%s?fields[%s]=test&include=foobar',
            $this->faker()->domainName,
            $resource->type(),
            $resource->id(),
            $resource->type()
        );
        $request = new Request('GET', new Uri($uri));

        $response = $this->createMock(ResponseInterface::class);
        $response->method('document')->willReturn($document);
        (new ResponseDecorator())->handle($request, $response);
        $this->assertTrue($document->data()->first()->attributes()->isEmpty());
    }

    /**
     * @throws BadRequestException
     */
    public function testCleanupRelationships(): void
    {
        $relatedResource = new Resource($this->faker()->slug, (string) $this->faker()->numberBetween(), [
            'name' => $this->faker()->domainName,
        ]);
        $relatedResource->relationships()->set(new Relationship($this->faker->slug));
        $relationship = new Relationship($this->faker->slug, $relatedResource);

        $resource = new Resource($this->faker()->slug, (string) $this->faker->numberBetween());
        $resource->relationships()->set($relationship);
        $document = new Document($resource);

        $uri = sprintf(
            'http://%s/%s/%s/relationships/%s',
            $this->faker()->domainName,
            $resource->type(),
            $resource->id(),
            $relationship->name()
        );
        $request = new Request('GET', new Uri($uri));

        $expected = clone $document;
        $expected->included()->merge($relatedResource);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('document')->willReturn($document);
        (new ResponseDecorator())->handle($request, $response);
        $this->assertEmpty($document->data()->first()->relationships()->all());
    }
}