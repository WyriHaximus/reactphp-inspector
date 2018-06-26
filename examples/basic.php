<?php

use React\EventLoop\Factory;
use WyriHaximus\React\Inspector\Metric;
use WyriHaximus\React\Inspector\Metrics;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$loop = Factory::create();

$metrics = new Metrics($loop, ['ticks'], 1);
$subscription = $metrics->subscribe(function (Metric $metric) {
    echo '[', (new DateTimeImmutable('@' . (int)$metric->getTime()))->format('r'), ']', $metric->getKey(), ': ', $metric->getValue(), PHP_EOL;
});

$loop->addTimer(300, function () use ($subscription) {
    $subscription->dispose();
});
$loop->run();
