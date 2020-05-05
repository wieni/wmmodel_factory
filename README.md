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
### Creating factories
Factories are the classes responsible for generating the data that will be
 used to fill the fields of the newly created entity.
 
Factories for certain entity type / bundle combinations can be added by 
 creating plugins with the `@EntityFactory` annotation, defining the entity 
 type ID in the `entity_type` parameter and the bundle in the `bundle` 
 parameter. The class should also implement the `EntityFactoryInterface` 
 interface.

It's also possible to create multiple factories for the same entity type /
 bundle combination by giving them unique names through the `name` 
 annotation parameter.

#### Example
```php
<?php

namespace Drupal\your_module\Entity\ModelFactory\Factory\Node;

use Drupal\your_module\Entity\Meta\Meta;
use Drupal\wmmodel_factory\EntityFactoryBase;

/**
 * @EntityFactory(
 *     entity_type = "node",
 *     bundle = "page"
 * )
 */
class PageFactory extends EntityFactoryBase
{
    public function make(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'menu_link' => null,
            'field_meta' => [
                'entity' => $this->faker->entity(Meta::class),
            ],
            'field_intro' => $this->faker->optional()->text(320),
        ];
    }
}
```

### Creating states
States allow you to define discrete modifications that can be applied to 
your model factories in any combination. For example, your Page model might
 have an `unpublished` state that modifies one of its default attribute values.
 
States for certain entity type / bundle combinations can be added by 
creating plugins with the `@EntityState` annotation, defining the entity 
type ID in the `entity_type` parameter, the bundle in the `bundle` 
parameter and a unique name in the `name` parameter. The class should also
 implement the `EntityStateInterface` interface.

#### Example
```php
<?php

namespace Drupal\your_module\Entity\ModelFactory\State\Node;

use Drupal\wmmodel_factory\EntityStateBase;

/**
 * @EntityState(
 *     name = "unpublished",
 *     entity_type = "node",
 * )
 */
class UnpublishedState extends EntityStateBase
{
    public function make(): array
    {
        return [
            'status' => 0,
        ];
    }
}
```

### Generating entities
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
