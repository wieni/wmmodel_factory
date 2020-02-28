<?php

namespace Drupal\wmmodel_factory;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\wmmodel\Factory\ModelFactoryInterface;
use Faker\Generator as Faker;

class Factory
{
    /** @var Faker */
    protected $faker;
    /** @var EntityFactoryManager */
    protected $factoryManager;
    /** @var EntityStateManager */
    protected $stateManager;
    /** @var ModelFactoryInterface */
    protected $modelFactory;
    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var EntityFieldManagerInterface */
    protected $entityFieldManager;

    /** @var array */
    protected $afterMaking = [];
    /** @var array */
    protected $afterCreating = [];
    /** @var string|null */
    protected $langcode = null;

    public function __construct(
        Faker $faker,
        EntityFactoryManager $factoryManager,
        EntityStateManager $stateManager,
        ModelFactoryInterface $modelFactory,
        EntityTypeManagerInterface $entityTypeManager,
        EntityFieldManagerInterface $entityFieldManager
    ) {
        $this->faker = $faker;
        $this->factoryManager = $factoryManager;
        $this->stateManager = $stateManager;
        $this->modelFactory = $modelFactory;
        $this->entityTypeManager = $entityTypeManager;
        $this->entityFieldManager = $entityFieldManager;
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
        [$entityType, $bundle] = $this->modelFactory->getEntityTypeAndBundle($class);
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
        [$entityType, $bundle] = $this->modelFactory->getEntityTypeAndBundle($class);
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
        [$entityType, $bundle] = $this->modelFactory->getEntityTypeAndBundle($class);
        return $this->of($entityType, $bundle)->make($attributes);
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
        [$entityType, $bundle] = $this->modelFactory->getEntityTypeAndBundle($class);

        return new FactoryBuilder(
            $this->faker,
            $this->entityTypeManager,
            $this->entityFieldManager,
            $this->factoryManager,
            $this->stateManager,
            $this,
            $entityType,
            $bundle,
            $name,
            $this->langcode,
            $this->afterMaking,
            $this->afterCreating
        );
    }
}
