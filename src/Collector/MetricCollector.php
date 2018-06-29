<?php declare(strict_types=1);

namespace WyriHaximus\React\Inspector\Collector;

use React\EventLoop\LoopInterface;
use Rx\ObservableInterface;
use WyriHaximus\React\Inspector\CollectorInterface;
use WyriHaximus\React\Inspector\GlobalState;
use WyriHaximus\React\Inspector\Metric;
use function ApiClients\Tools\Rx\observableFromArray;

final class MetricCollector implements CollectorInterface
{
    /**
     * @var LoopInterface
     */
    private $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function collect(): ObservableInterface
    {
        return observableFromArray([
            new Metric(
                'inspector.metrics',
                (float)count(GlobalState::get())
            ),
        ]);
    }

    public function cancel(): void
    {
        unset($this->loop);
        $this->loop = null;
    }
}
