<?php declare(strict_types=1);

namespace WyriHaximus\React\Inspector;

use function WyriHaximus\from_get_in_packages_composer;

final class GlobalState
{
    protected static $state = [];

    public static function bootstrap(): void
    {
        self::$state = array_fill_keys(
            array_values(
                iterator_to_array(
                    from_get_in_packages_composer(
                        'extra.react-inspector.metrics'
                    )
                )
            ),
            0.0
        );
    }

    public static function get(): array
    {
        return self::$state;
    }

    public static function reset(): void
    {
        foreach (self::$state as $key => $value) {
            self::$state[$key] = 0.0;
        }
    }

    public static function clear(): void
    {
        self::$state = [];
    }

    public static function set(string $key, float $value): void
    {
        self::$state[$key] = $value;
    }

    public static function incr(string $key, float $value = 1): void
    {
        if (!isset(self::$state[$key])) {
            self::$state[$key] = 0.0;
        }

        self::$state[$key] += $value;
    }

    public static function decr(string $key, float $value = 1): void
    {
        if (!isset(self::$state[$key])) {
            self::$state[$key] = 0.0;
        }

        self::$state[$key] -= $value;
    }
}
