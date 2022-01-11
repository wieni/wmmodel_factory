<?php

namespace Drupal\wmmodel_factory\Faker;

use Drupal\wmmodel_factory\Faker\Provider\DrupalEntity;
use Faker\Factory as FactoryBase;
use Faker\Generator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Factory implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function create(): Generator
    {
        $generator = FactoryBase::create();

        $generator->addProvider(
            new DrupalEntity(
                $generator,
                $this->container,
                $this->container->get('entity_type.manager'),
                $this->container->get('entity_type.repository')
            )
        );

        return $generator;
    }
}
