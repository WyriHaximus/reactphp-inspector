<?php declare(strict_types=1);

namespace WyriHaximus\React\Inspector;

use React\EventLoop\LoopInterface;
use Rx\ObservableInterface;

interface CollectorInterface
{
    /**
     * Create an instance, only an accepts an loop because the Metrics class only knows about the loop.
     *
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop);

    /**
     * Request a array of metrics.
     *
     * @return ObservableInterface<Metric[]>
     */
    public function collect(): ObservableInterface;

    /**
     * Cancel all outstanding operations.
     */
    public function cancel(): void;
}
