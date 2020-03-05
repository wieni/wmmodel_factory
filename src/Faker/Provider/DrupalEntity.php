<?php

namespace Drupal\wmmodel_factory\Faker\Provider;

use Drupal\wmmodel\Factory\ModelFactoryInterface;
use Drupal\wmmodel_factory\FactoryBuilder;
use Faker\Generator;
use Faker\Provider\Base;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DrupalEntity extends Base
{
    /** @var ContainerInterface */
    protected $container;
    /** @var ModelFactoryInterface */
    protected $modelFactory;

    public function __construct(
        Generator $generator,
        ContainerInterface $container,
        ModelFactoryInterface $modelFactory
    ) {
        parent::__construct($generator);
        $this->container = $container;
        $this->modelFactory = $modelFactory;
    }

    public function entity(string $class, ?string $name = null): FactoryBuilder
    {
        [$entityType, $bundle] = $this->modelFactory->getEntityTypeAndBundle($class);

        return $this->entityWithType($entityType, $bundle, $name);
    }

    public function entityWithType(string $entityType, ?string $bundle = null, ?string $name = null): FactoryBuilder
    {
        return FactoryBuilder::createInstance(
            $this->container,
            $entityType,
            $bundle ?? $entityType,
            $name
        );
    }
}
