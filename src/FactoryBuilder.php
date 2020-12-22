<?php

namespace Drupal\wmmodel_factory;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Faker\Generator as Faker;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FactoryBuilder
{
    /** @var Faker */
    protected $faker;
    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var EntityFieldManagerInterface */
    protected $entityFieldManager;
    /** @var EntityFactoryPluginManager */
    protected $factoryManager;
    /** @var EntityStatePluginManager */
    protected $stateManager;

    /** @var string */
    protected $entityType;
    /** @var string */
    protected $bundle;
    /** @var string|null */
    protected $name;
    /** @var string|null */
    protected $langcode;
    /** @var array */
    protected $afterMaking = [];
    /** @var array */
    protected $afterCreating = [];
    /** @var int|null */
    protected $amount;
    /** @var array */
    protected $activeStates = [];
    /** @var bool */
    protected $isCreating;

    public function __construct(
        Faker $faker,
        EntityTypeManagerInterface $entityTypeManager,
        EntityFieldManagerInterface $entityFieldManager,
        EntityFactoryPluginManager $factoryManager,
        EntityStatePluginManager $stateManager,
        string $entityType,
        string $bundle,
        ?string $name = null,
        ?string $langcode = null,
        array $afterMaking = [],
        array $afterCreating = []
    ) {
        $this->faker = $faker;
        $this->entityTypeManager = $entityTypeManager;
        $this->entityFieldManager = $entityFieldManager;
        $this->factoryManager = $factoryManager;
        $this->stateManager = $stateManager;
        $this->entityType = $entityType;
        $this->bundle = $bundle;
        $this->name = $name;
        $this->langcode = $langcode;
        $this->afterMaking = $afterMaking;
        $this->afterCreating = $afterCreating;
    }

    public static function createInstance(
        ContainerInterface $container,
        string $entityType,
        string $bundle,
        ?string $name = null,
        ?string $langcode = null,
        array $afterMaking = [],
        array $afterCreating = []
    ): self {
        return new static(
            $container->get('wmmodel_factory.faker.generator'),
            $container->get('entity_type.manager'),
            $container->get('entity_field.manager'),
            $container->get('plugin.manager.wmmodel_factory.factory'),
            $container->get('plugin.manager.wmmodel_factory.state'),
            $entityType,
            $bundle,
            $name,
            $langcode,
            $afterMaking,
            $afterCreating
        );
    }

    /** Set the amount of models you wish to create / make. */
    public function times(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /** Set the langcode of models you wish to create / make. */
    public function langcode(?string $langcode): self
    {
        $this->langcode = $langcode;

        return $this;
    }

    /** Set the state to be applied to the model. */
    public function state(string $state): self
    {
        return $this->states([$state]);
    }

    /**
     * Set the states to be applied to the model.
     *
     * @param array|mixed $states
     */
    public function states($states): self
    {
        $this->activeStates = is_array($states) ? $states : func_get_args();

        return $this;
    }

    /** Create a model and persist it in the database if requested. */
    public function lazy(array $attributes = []): callable
    {
        return function () use ($attributes) {
            return $this->create($attributes);
        };
    }

    /**
     * Create a collection of models and persist them to the database.
     *
     * @return ContentEntityInterface[]|ContentEntityInterface
     */
    public function create(array $attributes = [])
    {
        $this->isCreating = true;

        $results = $this->make($attributes);

        $collection = [];
        if (is_array($results)) {
            $collection = $results;
        } elseif ($results) {
            $collection = [$results];
        }

        $this->store($collection);
        $this->callAfterCreating($collection);

        unset($this->isCreating);

        return $results;
    }

    /**
     * Create a collection of models.
     *
     * @return ContentEntityInterface[]|ContentEntityInterface
     */
    public function make(array $attributes = [])
    {
        if ($this->amount === null) {
            $instance = $this->makeInstance($attributes);
            $this->callAfterMaking([$instance]);
            return $instance;
        }

        if ($this->amount < 1) {
            return [];
        }

        $instances = [];
        foreach (range(1, $this->amount) as $i) {
            $instances[] = $this->makeInstance($attributes);
        }
        $this->callAfterMaking($instances);

        return $instances;
    }

    /**
     * Create an array of raw attribute arrays.
     *
     * @return mixed
     */
    public function raw(array $attributes = [])
    {
        if ($this->amount === null) {
            return $this->getRawAttributes($attributes);
        }

        if ($this->amount < 1) {
            return [];
        }

        return array_map(function () use ($attributes): array {
            return $this->getRawAttributes($attributes);
        }, range(1, $this->amount));
    }

    /**
     * Run after making callbacks on a collection of models.
     *
     * @param ContentEntityInterface[] $models
     */
    public function callAfterMaking(array $models): void
    {
        $this->callAfter($this->afterMaking, $models);
    }

    /**
     * Run after creating callbacks on a collection of models.
     *
     * @param ContentEntityInterface[] $models
     */
    public function callAfterCreating($models): void
    {
        $this->callAfter($this->afterCreating, $models);
    }

    /** Set the connection name on the results and store them. */
    protected function store(array $models): void
    {
        foreach ($models as $model) {
            $model->save();
        }
    }

    protected function getFactoryName(bool $exceptionOnInvalid = true): ?string
    {
        $factoryNames = $this->factoryManager->getNamesByEntityType($this->entityType, $this->bundle);

        if (in_array($this->name, $factoryNames, true)) {
            return $this->name;
        }

        if (in_array('default', $factoryNames, true)) {
            return 'default';
        }

        if (!empty($factoryNames)) {
            return reset($factoryNames);
        }

        if (!$exceptionOnInvalid) {
            return null;
        }

        throw new \InvalidArgumentException("Unable to locate factory for entity with type {$this->entityType} and bundle {$this->bundle}.");
    }

    protected function getFactory(): EntityFactoryInterface
    {
        $pluginId = implode('.', [$this->entityType, $this->bundle, $this->getFactoryName()]);

        return $this->factoryManager->createInstance($pluginId);
    }

    /**
     * Get a raw attributes array for the model.
     *
     * @throws \InvalidArgumentException
     */
    protected function getRawAttributes(array $attributes = []): array
    {
        $definition = $this->getFactory()->make();
        $definition = $this->applyStates($definition);
        $attributes = array_merge($definition, $attributes);
        $attributes = $this->expandAttributes($attributes);
        $attributes = $this->addDrupalAttributes($attributes);

        return $attributes;
    }

    /** Make an instance of the model with the given attributes. */
    protected function makeInstance(array $attributes = []): ContentEntityInterface
    {
        $storage = $this->entityTypeManager->getStorage($this->entityType);
        $attributes = $this->getRawAttributes($attributes);

        return $storage->create($attributes);
    }

    /**
     * Apply the active states to the model definition array.
     *
     * @throws \InvalidArgumentException
     */
    protected function applyStates(array $definition): array
    {
        foreach ($this->activeStates as $state) {
            $pluginId = $this->stateManager->getPluginIdByEntityType($this->entityType, $this->bundle, $state);

            if (!$pluginId) {
                if ($this->stateHasAfterCallback($state)) {
                    continue;
                }

                throw new \InvalidArgumentException("Unable to locate [{$state}] state for [{$this->entityType}] [{$this->bundle}].");
            }

            $definition = array_merge(
                $definition,
                $this->stateManager->createInstance($pluginId)->make()
            );
        }

        return $definition;
    }

    /** Expand all attributes to their underlying values. */
    protected function expandAttributes(array $attributes): array
    {
        foreach ($attributes as &$attribute) {
            if (is_callable($attribute) && !is_string($attribute) && !is_array($attribute)) {
                $attribute = $attribute($attributes);
            }

            if (is_a($attribute, self::class)) {
                $attribute = $this->isCreating
                    ? $attribute->create()
                    : $attribute->make();
            }

            if (is_array($attribute)) {
                $attribute = $this->expandAttributes($attribute);
            }
        }

        return $attributes;
    }

    protected function addDrupalAttributes(array $attributes): array
    {
        $entityType = $this->entityTypeManager->getDefinition($this->entityType);
        $fieldDefinitions = $this->entityFieldManager->getFieldDefinitions($this->entityType, $this->bundle);

        if ($key = $entityType->getKey('bundle')) {
            $attributes[$key] = $this->bundle;
        }

        if (
            ($key = $entityType->getKey('langcode'))
            && !isset($attributes[$key])
            && $this->langcode
        ) {
            $attributes[$key] = $this->langcode;
        }

        /**
         * TODO: Remove this if issue is fixed
         * @see https://www.drupal.org/project/drupal/issues/2915034
         */
        if (
            ($key = $entityType->getKey('default_langcode'))
            && !isset($attributes[$key])
        ) {
            $attributes[$key] = true;
        }

        if (
            isset($fieldDefinitions['content_translation_source'])
            && !isset($attributes['content_translation_source'])
        ) {
            $attributes['content_translation_source'] = 'und';
        }

        return $attributes;
    }

    /**
     * Call after callbacks for each model and state.
     *
     * @param ContentEntityInterface[] $models
     */
    protected function callAfter(array $afterCallbacks, array $models): void
    {
        $states = array_merge([$this->getFactoryName()], $this->activeStates);

        foreach ($models as $model) {
            foreach ($states as $state) {
                $this->callAfterCallbacks($afterCallbacks, $model, $state);
            }
        }
    }

    /** Call after callbacks for each model and state. */
    protected function callAfterCallbacks(array $afterCallbacks, ContentEntityInterface $model, string $state): void
    {
        if (!isset($afterCallbacks[$this->entityType][$this->bundle][$state])) {
            return;
        }

        foreach ($afterCallbacks[$this->entityType][$this->bundle][$state] as $callback) {
            $callback($model, $this->faker);
        }
    }

    /** Determine if the given state has an "after" callback. */
    protected function stateHasAfterCallback(string $state): bool
    {
        return isset($this->afterMaking[$this->entityType][$this->bundle][$state])
            || isset($this->afterCreating[$this->entityType][$this->bundle][$state]);
    }
}
