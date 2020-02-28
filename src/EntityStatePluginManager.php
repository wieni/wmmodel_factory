<?php

namespace Drupal\wmmodel_factory;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\wmmodel_factory\Annotation\EntityState;

class EntityStatePluginManager extends DefaultPluginManager
{
    public function __construct(
        \Traversable $namespaces,
        CacheBackendInterface $cacheBackend,
        ModuleHandlerInterface $moduleHandler
    ) {
        parent::__construct(
            'EntityFactory/State',
            $namespaces,
            $moduleHandler,
            EntityFactoryInterface::class,
            EntityState::class
        );
        $this->alterInfo('wmmodel_factory_state_info');
        $this->setCacheBackend($cacheBackend, 'wmmodel_factory_state_info_plugins');
    }
}
