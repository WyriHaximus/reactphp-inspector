<?php declare(strict_types=1);

namespace WyriHaximus\React\Inspector;

use Rx\Subject\Subject;

interface MetricsStreamInterface
{
    /**
     * Any implementers of this interface should extend Subject and only call onNext with instances of Metric.
     */
}
