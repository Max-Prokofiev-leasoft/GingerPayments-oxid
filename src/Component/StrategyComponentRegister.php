<?php

namespace GingerPayments\Payments\Component;

use GingerPayments\Payments\Interfaces\StrategyInterface\BaseStrategy;

/**
 * Class StrategyComponentRegister
 *
 * This class is responsible for registering and retrieving strategy components.
 **/
class StrategyComponentRegister
{
    /**
     * @var array
     * Array to hold registered components.
     */
    protected static array $components = [];

    /**
     * Registers a component with a specific key.
     *
     * @param string $key
     * Key to associate with the component.
     * @param object $component
     * Component to be registered.
     * @return void
     */
    public static function register(string $key, object $component): void
    {
        self::$components[$key] = $component;
    }

    /**
     * Retrieves a registered component by its key.
     *
     * @template T of BaseStrategy
     * @param class-string<T> $key
     * Key associated with the component.
     * @return T|null
     * Returns the component if found, otherwise null.
     */
    public static function get(string $key)
    {
        return self::$components[$key] ?? null;
    }
}
