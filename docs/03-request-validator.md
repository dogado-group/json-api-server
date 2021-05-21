[back to README](../README.md)

# Request validator

As soon as you are creating your first Request Handler and try to fetch the request body, you will soon run into the
issue of having to validate that the request document exists and is not empty, and you will need to do this and similar
checks many times. That's why there is a class called `Dogado\JsonApi\Server\Validator\RequestValidator`.

The request validator offers you a stack of common assertions on the request body which should make your life easier.
In case of an error, it will directly throw a `JsonApiException`.

Method signature                                                                     | Description
-------------------------------------------------------------------------------------|--------------
assertDocument(RequestInterface $request): void                                      | Assert that the request contains a document.
assertNoDocument(RequestInterface $request): void                                    | Assert that the request contains no document.
assertDataNotEmpty(RequestInterface $request): void                                  | Assert that the request document contains resources.
assertResourcesMatchTypeAndContainIds(RequestInterface $request, string $type): void | Assert that the given request document resources match a certain type and contain IDs.
assertScalarResultWithId(RequestInterface $request, string $type): void              | Assert that exactly one resource of a certain type with id is present in the request document.
assertScalarResultWithoutId(RequestInterface $request, string $type): void           | Assert that exactly one resource of a certain type without id is present in the request document.

*****

[prev: Request handler](02-request-handler.md) | [back to README](../README.md) | [next: Exception handling](04-exception-handling.md)
