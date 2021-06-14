[back to README](../README.md)

# Exception handling

You should catch all exceptions and transform them via `JsonApiServer::handleException` into a json api response, which can be handled like a normal document response, unless you have no custom JSON API exception conversion.

*****

[prev: Request validator](03-request-validator.md) | [back to README](../README.md) | [next: Validator for data models](05-validator-for-data-models.md)
