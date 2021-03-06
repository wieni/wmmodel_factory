<?php

namespace Drupal\wmmodel_factory;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\wmmodel_factory\Annotation\EntityState;

/**
 * @method EntityStateInterface createInstance($plugin_id, array $configuration = [])
 */
class EntityStatePluginManager extends DefaultPluginManager
{
    public function __construct(
        \Traversable $namespaces,
        CacheBackendInterface $cacheBackend,
        ModuleHandlerInterface $moduleHandler
    ) {
        parent::__construct(
            '',
            $namespaces,
            $moduleHandler,
            EntityStateInterface::class,
            EntityState::class
        );
        $this->alterInfo('wmmodel_factory_state_info');
        $this->setCacheBackend($cacheBackend, 'wmmodel_factory_state_info_plugins');
    }

    public function getPluginIdByEntityType(string $entityType, string $bundle, string $name = 'default'): ?string
    {
        $ids = [
            "{$entityType}.{$bundle}.{$name}",
            "{$entityType}.{$name}",
        ];

        foreach ($ids as $id) {
            if ($this->hasDefinition($id)) {
                return $id;
            }
        }

        return null;
    }

    public function getDefinitionsByEntityType(string $entityType, ?string $bundle = null): array
    {
        return array_filter(
            $this->getDefinitions(),
            static function (array $definition) use ($entityType, $bundle): bool {
                if ($bundle && isset($definition['bundle'])) {
                    return $definition['entity_type'] === $entityType
                        && $definition['bundle'] === $bundle;
                }

                return $definition['entity_type'] === $entityType;
            }
        );
    }
}
