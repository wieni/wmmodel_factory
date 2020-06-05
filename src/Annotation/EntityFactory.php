<?php

namespace Drupal\wmmodel_factory\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * @Annotation
 */
class EntityFactory extends Plugin
{
    /** @var string */
    public $label;
    /** @var string */
    public $entity_type;
    /** @var string */
    public $bundle;
    /** @var string */
    public $name = 'default';

    public function getId()
    {
        if (isset($this->definition['entity_type'], $this->definition['bundle'])) {
            return implode('.', [
                $this->definition['entity_type'],
                $this->definition['bundle'],
                $this->definition['name'],
            ]);
        }

        return parent::getId();
    }
}
