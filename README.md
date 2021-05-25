# Abstract server side php implementation of the JSON:API protocol.

[![phpunit](https://github.com/dogado-group/json-api-server/actions/workflows/phpunit.yml/badge.svg)](https://github.com/dogado-group/json-api-server/actions/workflows/phpunit.yml)
[![Coverage Status](https://coveralls.io/repos/github/dogado-group/json-api-server/badge.svg?branch=main)](https://coveralls.io/github/dogado-group/json-api-server?branch=main)
[![Total Downloads](https://poser.pugx.org/dogado/json-api-server/downloads)](https://packagist.org/packages/dogado/json-api-server)
[![Latest Stable Version](https://poser.pugx.org/dogado/json-api-server/v/stable)](https://packagist.org/packages/dogado/json-api-server)
[![Latest Unstable Version](https://poser.pugx.org/dogado/json-api-server/v/unstable.png)](https://packagist.org/packages/dogado/json-api-server)
[![License](https://poser.pugx.org/dogado/json-api-server/license)](https://packagist.org/packages/dogado/json-api-server)

## Installation

```sh
composer require dogado/json-api-server
```

## Documentation

First you should read the docs of [dogado/json-api-common](https://github.com/dogado-group/json-api-common/tree/main/docs) where all basic structures will be explained.

1. [JsonApiServer](docs/01-json-api-server.md)
1. [Request handler](docs/02-request-handler.md)
1. [Request validator](docs/03-request-validator.md)
1. [Exception handling](docs/04-exception-handling.md)
1. [Validator for data models](docs/05-validator-for-data-models.md)

## Credits

- [Chris Döhring](https://github.com/chris-doehring)
- [Philipp Marien](https://github.com/pmarien)
- [eosnewmedia team](https://github.com/eosnewmedia)

This package contains code taken from [enm/json-api-server](https://github.com/eosnewmedia/JSON-API-Server).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
