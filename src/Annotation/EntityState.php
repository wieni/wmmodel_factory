<?php

namespace Drupal\wmmodel_factory\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * @Annotation
 */
class EntityState extends Plugin
{
    /** @var string */
    public $entity_type;
    /** @var string */
    public $bundle;
    /** @var string */
    public $name;

    public function getId()
    {
        if (isset($this->definition['entity_type'], $this->definition['name'])) {
            $name[] = $this->definition['entity_type'];

            if (!empty($this->definition['bundle'])) {
                $name[] = $this->definition['bundle'];
            }

            $name[] = $this->definition['name'];

            return implode('.', $name);
        }

        return parent::getId();
    }
}
