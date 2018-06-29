<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Inspector\Collector;

use ApiClients\Tools\TestUtilities\TestCase;
use React\EventLoop\Factory;
use Rx\React\Promise;
use WyriHaximus\React\Inspector\Collector\MetricCollector;
use WyriHaximus\React\Inspector\GlobalState;
use WyriHaximus\React\Inspector\Metric;

final class MetricCollectorTest extends TestCase
{
    public function setUp()
    {
        GlobalState::clear();
        parent::setUp();
    }

    public function testBasics()
    {
        $collector = new MetricCollector(Factory::create());

        /** @var Metric $metric */
        $metric = $this->await(Promise::fromObservable($collector->collect()));
        self::assertSame('inspector.metrics', $metric->getKey());
        self::assertSame(0.0, $metric->getValue());

        GlobalState::incr('key', 32.10);

        /** @var Metric $metric */
        $metric = $this->await(Promise::fromObservable($collector->collect()));
        self::assertSame('inspector.metrics', $metric->getKey());
        self::assertSame(1.0, $metric->getValue());
    }
}
