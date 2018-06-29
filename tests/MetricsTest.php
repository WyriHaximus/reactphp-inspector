<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Inspector;

use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use WyriHaximus\React\Inspector\GlobalState;
use WyriHaximus\React\Inspector\Metric;
use WyriHaximus\React\Inspector\Metrics;
use function Clue\React\Block\await;
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

        self::assertCount(20, $metricsCollection);
        /** @var Metric $metric */
        foreach ($metricsCollection as $index => $metric) {
            self::assertTrue(
                in_array(
                    $metric->getKey(),
                    [
                        'inspector.metrics',
                        'memory.external',
                        'memory.external_peak',
                        'memory.internal',
                        'memory.internal_peak',
                    ],
                    true
                )
            );
            self::assertTrue(
                $begin < $metric->getTime() &&
                $end > $metric->getTime()
            );
        }

        self::assertSame(0.0, $metricsCollection[0]->getValue());
        self::assertSame(5.0, $metricsCollection[5]->getValue());
        self::assertSame(5.0, $metricsCollection[10]->getValue());
        self::assertSame(5.0, $metricsCollection[15]->getValue());
    }
}
