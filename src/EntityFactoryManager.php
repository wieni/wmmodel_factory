<?php

namespace Drupal\wmmodel_factory;

class EntityFactoryManager
{
    /** @var EntityFactoryPluginManager */
    protected $pluginManager;

    public function __construct(
        EntityFactoryPluginManager $pluginManager
    ) {
        $this->pluginManager = $pluginManager;
    }

    public function getDefinition(string $entityType, string $bundle, string $name = 'default'): ?EntityFactoryInterface
    {
        $ids = [
            "{$entityType}.{$bundle}.{$name}",
            "{$entityType}.{$name}",
        ];

        foreach ($ids as $id) {
            if ($this->pluginManager->hasDefinition($id)) {
                return $this->pluginManager->createInstance($id);
            }
        }

        return null;
    }
}
