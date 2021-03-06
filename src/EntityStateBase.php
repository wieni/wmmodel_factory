<?php

namespace Drupal\wmmodel_factory;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\wmmodel_factory\Faker\Provider\DrupalEntity;
use Faker\Generator as Faker;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class EntityStateBase extends PluginBase implements EntityStateInterface, ContainerFactoryPluginInterface
{
    /** @var Faker|DrupalEntity */
    protected $faker;

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $pluginId,
        $pluginDefinition
    ) {
        $instance = new static($configuration, $pluginId, $pluginDefinition);
        $instance->faker = $container->get('wmmodel_factory.faker.generator');

        return $instance;
    }

    abstract public function make(): array;
}
