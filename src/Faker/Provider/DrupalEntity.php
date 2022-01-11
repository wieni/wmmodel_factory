<?php

namespace Drupal\wmmodel_factory\Faker\Provider;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeRepositoryInterface;
use Drupal\wmmodel_factory\FactoryBuilder;
use Faker\Generator;
use Faker\Provider\Base;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DrupalEntity extends Base
{
    /** @var ContainerInterface */
    protected $container;
    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var EntityTypeRepositoryInterface */
    protected $entityTypeRepository;

    public function __construct(
        Generator $generator,
        ContainerInterface $container,
        EntityTypeManagerInterface $entityTypeManager,
        EntityTypeRepositoryInterface $entityTypeRepository
    ) {
        parent::__construct($generator);
        $this->container = $container;
        $this->entityTypeManager = $entityTypeManager;
        $this->entityTypeRepository = $entityTypeRepository;
    }

    public function entity(string $class, ?string $name = null): FactoryBuilder
    {
        $entityTypeId = $this->entityTypeRepository->getEntityTypeFromClass($class);
        $storage = $this->entityTypeManager->getStorage($entityTypeId);
        $bundle = $storage->getBundleFromClass($class);

        return $this->entityWithType($entityTypeId, $bundle, $name);
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
