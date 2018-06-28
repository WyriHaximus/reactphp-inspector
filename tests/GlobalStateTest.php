<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Inspector;

use PHPUnit\Framework\TestCase;
use WyriHaximus\React\Inspector\GlobalState;

final class GlobalStateTest extends TestCase
{
    public function setUp()
    {
        GlobalState::clear();
    }

    public function testBootstrappedState()
    {
        GlobalState::bootstrap();
        self::assertSame([
            'inspector.metrics' => 0.0,
        ], GlobalState::get());
    }

    public function testGlobalState()
    {
        self::assertSame([], GlobalState::get());
        GlobalState::set('key', 1.0);
        self::assertSame(['key' => 1.0], GlobalState::get());
        GlobalState::incr('key');
        self::assertSame(['key' => 2.0], GlobalState::get());
        GlobalState::incr('key', 3.0);
        self::assertSame(['key' => 5.0], GlobalState::get());
        GlobalState::reset();
        self::assertSame(['key' => 0.0], GlobalState::get());
        GlobalState::clear();
        self::assertSame([], GlobalState::get());
        GlobalState::incr('key', 3.0);
        self::assertSame(['key' => 3.0], GlobalState::get());
        GlobalState::decr('key');
        self::assertSame(['key' => 2.0], GlobalState::get());
    }
}
