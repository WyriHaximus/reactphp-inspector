<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Inspector;

use PHPUnit\Framework\TestCase;
use WyriHaximus\React\Inspector\Metric;

final class MetricTest extends TestCase
{
    public function testBasics()
    {
        $key = 'key';
        $value = 123.45;
        $time = microtime(true);
        $metric = new Metric($key, $value, $time);
        self::assertSame($key, $metric->getKey());
        self::assertSame($value, $metric->getValue());
        self::assertSame($time, $metric->getTime());
    }

    public function testOmittingTime()
    {
        $key = 'key';
        $value = 123.45;
        $timeBefore = microtime(true);
        $metric = new Metric($key, $value);
        $timeAfter = microtime(true);
        self::assertSame($key, $metric->getKey());
        self::assertSame($value, $metric->getValue());
        self::assertTrue($metric->getTime() >= $timeBefore && $metric->getTime() <= $timeAfter);
    }
}
