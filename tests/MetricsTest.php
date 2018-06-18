<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Inspector;

use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use function Clue\React\Block\await;
use WyriHaximus\React\Inspector\GlobalState;
use WyriHaximus\React\Inspector\Metric;
use WyriHaximus\React\Inspector\Metrics;
use function WyriHaximus\React\timedPromise;

final class MetricsTest extends TestCase
{
    public function setUp()
    {
        GlobalState::clear();
    }

    public function testBasic()
    {
        $loop = Factory::create();

        $metricsCollection = [];
        $loop->futureTick(function () use ($loop, &$metricsCollection) {
            $metrics = new Metrics($loop, ['ticks'], 1);
            $metrics->subscribe(function ($metric) use (&$metricsCollection) {
                $metricsCollection[] = $metric;
            });
        });

        $begin = microtime(true);
        await(timedPromise($loop, 5), $loop, 10);
        $end = microtime(true);

        self::assertCount(3, $metricsCollection);
        /** @var Metric $metric */
        foreach ($metricsCollection as $metric) {
            self::assertSame('inspector.metrics', $metric->getKey());
            self::assertTrue(
                $begin < $metric->getTime() &&
                $end > $metric->getTime()
            );
        }
        self::assertSame(0.0, $metricsCollection[0]->getValue());
        self::assertSame(1.0, $metricsCollection[1]->getValue());
        self::assertSame(1.0, $metricsCollection[2]->getValue());
    }
}
