<?php

namespace Drupal\wmmodel_factory;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeRepositoryInterface;
use Drupal\Core\Entity\Exception\AmbiguousEntityClassException;
use Drupal\Core\Entity\Exception\NoCorrespondingEntityClassException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Factory implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var EntityTypeRepositoryInterface */
    protected $entityTypeRepository;

    /** @var array */
    protected $afterMaking = [];
    /** @var array */
    protected $afterCreating = [];
    /** @var string|null */
    protected $langcode = null;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        EntityTypeRepositoryInterface $entityTypeRepository
    ) {
        $this->entityTypeManager = $entityTypeManager;
        $this->entityTypeRepository = $entityTypeRepository;
    }

    /** Set the default langcode of models you wish to create / make. */
    public function setLangcode(?string $langcode): self
    {
        $this->langcode = $langcode;

        return $this;
    }

    /** Get the default langcode of models you wish to create / make. */
    public function getLangcode(): ?string
    {
        return $this->langcode;
    }

    /** Define a callback to run after making a model. */
    public function afterMaking(string $class, callable $callback, string $name = 'default'): self
    {
        [$entityType, $bundle] = $this->getEntityTypeAndBundle($class);
        $this->afterMaking[$entityType][$bundle][$name][] = $callback;

        return $this;
    }

    /** Define a callback to run after making a model with given state. */
    public function afterMakingState(string $class, string $state, callable $callback): self
    {
        return $this->afterMaking($class, $callback, $state);
    }

    /** Define a callback to run after creating a model. */
    public function afterCreating(string $class, callable $callback, string $name = 'default'): self
    {
        [$entityType, $bundle] = $this->getEntityTypeAndBundle($class);
        $this->afterCreating[$entityType][$bundle][$name][] = $callback;

        return $this;
    }

    /** Define a callback to run after creating a model with given state. */
    public function afterCreatingState(string $class, string $state, callable $callback): self
    {
        return $this->afterCreating($class, $callback, $state);
    }

    /**
     * Create an instance of the given model and persist it to the database.
     *
     * @return ContentEntityInterface[]|ContentEntityInterface
     */
    public function create(string $class, array $attributes = [])
    {
        return $this->of($class)->create($attributes);
    }

    /**
     * Create an instance of the given model and type and persist it to the database.
     *
     * @return ContentEntityInterface[]|ContentEntityInterface
     */
    public function createAs(string $class, string $name, array $attributes = [])
    {
        return $this->of($class, $name)->create($attributes);
    }

    /**
     * Create an instance of the given model.
     *
     * @return ContentEntityInterface[]|ContentEntityInterface
     */
    public function make(string $class, array $attributes = [])
    {
        return $this->of($class)->make($attributes);
    }

    /**
     * Create an instance of the given model and type.
     *
     * @return ContentEntityInterface[]|ContentEntityInterface
     */
    public function makeAs(string $class, string $name, array $attributes = [])
    {
        return $this->of($class, $name)->make($attributes);
    }

    /** Create a builder for the given model. */
    public function of(string $class, string $name = 'default'): FactoryBuilder
    {
        [$entityType, $bundle] = $this->getEntityTypeAndBundle($class);

        return $this->ofType($entityType, $bundle ?? $entityType, $name);
    }

    /** Create a builder for the given model. */
    public function ofType(string $entityTypeId, string $bundle, string $name = 'default'): FactoryBuilder
    {
        return FactoryBuilder::createInstance(
            $this->container,
            $entityTypeId,
            $bundle,
            $name,
            $this->langcode,
            $this->afterMaking,
            $this->afterCreating
        );
    }

    protected function getEntityTypeAndBundle(string $className): array
    {
        $entityTypeId = $this->entityTypeRepository->getEntityTypeFromClass($className);
        $storage = $this->entityTypeManager->getStorage($entityTypeId);
        $bundle = $storage->getBundleFromClass($className);

        return [$entityTypeId, $bundle];
    }
}
