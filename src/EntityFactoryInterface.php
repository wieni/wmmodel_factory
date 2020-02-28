<?php

namespace Drupal\wmmodel_factory;

use Drupal\Component\Plugin\PluginInspectionInterface;

interface EntityFactoryInterface extends PluginInspectionInterface
{
    public function make(array $attributes = []): array;
}
