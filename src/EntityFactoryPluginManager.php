<?php

namespace Drupal\wmmodel_factory;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\wmmodel_factory\Annotation\EntityFactory;

class EntityFactoryPluginManager extends DefaultPluginManager
{
    public function __construct(
        \Traversable $namespaces,
        CacheBackendInterface $cacheBackend,
        ModuleHandlerInterface $moduleHandler
    ) {
        parent::__construct(
            'EntityFactory/Factory',
            $namespaces,
            $moduleHandler,
            EntityFactoryInterface::class,
            EntityFactory::class
        );
        $this->alterInfo('wmmodel_factory_factory_info');
        $this->setCacheBackend($cacheBackend, 'wmmodel_factory_factory_info_plugins');
    }
}
