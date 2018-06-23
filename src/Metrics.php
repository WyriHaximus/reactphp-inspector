<?php declare(strict_types=1);

namespace WyriHaximus\React\Inspector;

use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use Rx\DisposableInterface;
use Rx\ObserverInterface;
use Rx\Subject\Subject;
use function WyriHaximus\get_in_packages_composer;

final class Metrics extends Subject implements MetricsStreamInterface
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var string[]
     */
    private $resetGroups;

    /**
     * @var string[][]
     */
    private $resetGroupsMetrics;

    /**
     * @var float
     */
    private $interval;

    /**
     * @var TimerInterface|null
     */
    private $timer;

    /**
     * @param LoopInterface $loop
     * @param string[]      $resetGroups
     * @param float         $interval
     */
    public function __construct(LoopInterface $loop, array $resetGroups, float $interval)
    {
        $this->loop = $loop;
        $this->resetGroups = $resetGroups;
        $this->interval = $interval;

        $this->setUpResetGroups();
    }

    public function removeObserver(ObserverInterface $observer): bool
    {
        $return = parent::removeObserver($observer);
        if (!$this->hasObservers()) {
            $this->loop->cancelTimer($this->timer);
            $this->timer = null;
        }

        return $return;
    }

    protected function _subscribe(ObserverInterface $observer): DisposableInterface
    {
        if ($this->timer === null) {
            $this->timer = $this->loop->addPeriodicTimer($this->interval, function () {
                $this->tick();
            });
        }

        return parent::_subscribe($observer);
    }

    private function setUpResetGroups()
    {
        foreach (get_in_packages_composer('extra.react-inspector.reset') as $package => $resetMetricGroups) {
            foreach ($resetMetricGroups as $group => $metrics) {
                foreach ($metrics as $metric) {
                    $this->resetGroupsMetrics[$group][$metric] = $metric;
                }
            }
        }
    }

    private function tick()
    {
        $time = microtime(true);
        $state = GlobalState::get();
        foreach ($this->resetGroups as $group) {
            if (!isset($this->resetGroupsMetrics[$group])) {
                continue;
            }

            foreach ($this->resetGroupsMetrics[$group] as $metric) {
                GlobalState::set($metric, 0);
            }
        }

        foreach ($state as $key => $value) {
            $this->onNext(
                new Metric(
                    $key,
                    (float)$value,
                    $time
                )
            );
            GlobalState::incr('inspector.metrics');
        }
    }
}
