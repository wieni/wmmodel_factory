wmmodel_factory
======================

[![Latest Stable Version](https://poser.pugx.org/wieni/wmmodel_factory/v/stable)](https://packagist.org/packages/wieni/wmmodel_factory)
[![Total Downloads](https://poser.pugx.org/wieni/wmmodel_factory/downloads)](https://packagist.org/packages/wieni/wmmodel_factory)
[![License](https://poser.pugx.org/wieni/wmmodel_factory/license)](https://packagist.org/packages/wieni/wmmodel_factory)

> Define factories that allow you to generate entities with certain fields, 
> filled with random data

## Why?
- The built-in way to generate random entities, `ContentEntityStorageInterface::createWithSampleValues`, is lacking in a lot of ways: it is untested, has a lot of issues and is not very flexible.
- Use the same API as [Laravel's model factories](https://laravel.com/docs/master/database-testing#writing-factories)
- Generate realistic content tailored to the entity type using the [Faker](https://github.com/fzaninotto/Faker) library. 

## Installation
This package requires PHP 7.1 and Drupal 8 or higher. It can be
installed using Composer:

```bash
 composer require wieni/wmmodel_factory
```

## How does it work?
_TODO_

## Changelog
All notable changes to this project will be documented in the
[CHANGELOG](CHANGELOG.md) file.

## Security
If you discover any security-related issues, please email
[security@wieni.be](mailto:security@wieni.be) instead of using the issue
tracker.

## License
Distributed under the MIT License. See the [LICENSE](LICENSE) file
for more information.
