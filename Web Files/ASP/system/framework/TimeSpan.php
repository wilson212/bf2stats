<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
namespace System;
use Stringable;
use ArgumentException;

/**
 * Represents a time interval.
 *
 * @package System
 */
class TimeSpan implements Stringable
{
    protected int $seconds;

    /**
     * Constructor
     *
     * @param int $hours
     * @param int $mins
     * @param int $secs secs - an amount of seconds, absolute value is used
     *
     * @throws ArgumentException if the argument passed is not numeric
     */
    public function __construct($hours, $mins, $secs)
    {
        if (!is_numeric($hours)) {
            throw new ArgumentException('Given argument is not an integer: ' . gettype($hours), 'hours');
        }

        if (!is_numeric($mins)) {
            throw new ArgumentException('Given argument is not an integer: ' . gettype($mins), 'mins');
        }

        if (!is_numeric($secs)) {
            throw new ArgumentException('Given argument is not an integer: ' . gettype($secs), 'secs');
        }

        // Append seconds
        $this->seconds = abs($secs);

        // Append hours
        if ($hours > 0) {
            $this->seconds += abs($hours * 3600);
        }

        // Append minutes
        if ($mins > 0) {
            $this->seconds += abs($mins * 60);
        }
    }

    /**
     * Add a TimeSpan
     *
     *
     * @throws ArgumentException
     */
    public function add(TimeSpan $span): static
    {
        $this->seconds += $span->seconds;
        return $this;
    }

    /**
     * Subtracts a TimeSpan from this current TimeSpan object
     *
     *
     *
     * @throws ArgumentException
     */
    public function subtract(TimeSpan $span): static
    {
        // Check for new negative value
        if ($span->seconds > $this->seconds)
        {
            throw new ArgumentException('Cannot subtract ' . $span->toString() . ' from ' . $this->toString());
        }

        $this->seconds -= $span->seconds;
        return $this;
    }

    /**
     * Gets a timespan from seconds
     *
     * @param int $seconds
     */
    public static function FromSeconds($seconds): self
    {
        return new self(0, 0, $seconds);
    }

    /**
     * Gets a timespan from minutes
     *
     * @param int $minutes
     */
    public static function FromMinutes($minutes): self
    {
        return new self(0, $minutes, 0);
    }

    /**
     * Gets a timespan from hours
     *
     * @param int $hours
     */
    public static function FromHours($hours): self
    {
        return new self($hours, 0, 0);
    }

    /**
     * Gets a timespan from days
     *
     * @param int $days
     */
    public static function days($days): self
    {
        return new self(0, 0, $days * 86400);
    }

    /**
     * Gets a timespan from weeks
     *
     * @param int $weeks
     */
    public static function FromWeeks($weeks): self
    {
        return new self(0, 0, $weeks * 604800);
    }

    /**
     * Returns this span of time in seconds
     *
     * @return  int
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * Returns the amount of 'whole' seconds in this
     * span of time
     */
    public function getWholeSeconds(): int
    {
        return $this->seconds % 60;
    }

    /**
     * Return an amount of minutes less than or equal
     * to this span of time
     */
    public function getMinutes(): int
    {
        return (int)floor($this->seconds / 60);
    }

    /**
     * Returns a float value representing this span of time
     * in minutes
     *
     * @return  float
     */
    public function getMinutesFloat(): int|float
    {
        return $this->seconds / 60;
    }

    /**
     * Returns the amount of 'whole' minutes in this
     * span of time
     */
    public function getWholeMinutes(): int
    {
        return (int)floor(($this->seconds % 3600) / 60);
    }

    /**
     * Adds an amount of minutes to this span of time
     *
     * @param int $mins
     */
    public function addMinutes($mins): void
    {
        $this->seconds += (int)$mins * 60;
    }

    /**
     * Returns an amount of hours less than or equal
     * to this span of time
     */
    public function getHours(): int
    {
        return (int)floor($this->seconds / 3600);
    }

    /**
     * Returns a float value representing this span of time
     * in hours
     *
     * @return  float
     */
    public function getHoursFloat(): int|float
    {
        return $this->seconds / 3600;
    }

    /**
     * Returns the amount of 'whole' hours in this
     * span of time
     */
    public function getWholeHours(): int
    {
        return (int)floor(($this->seconds % 86400) / 3600);
    }

    /**
     * Adds an amount of Hours to this span of time
     *
     * @param int $hours
     */
    public function addHours($hours): void
    {
        $this->seconds += (int)$hours * 3600;
    }

    /**
     * Returns an amount of days less than or equal
     * to this span of time
     */
    public function getDays(): int
    {
        return (int)floor($this->seconds / 86400);
    }

    /**
     * Returns a float value representing this span of time
     * in days
     *
     * @return  float
     */
    public function getDaysFloat(): int|float
    {
        return $this->seconds / 86400;
    }

    /**
     * Returns the amount of 'whole' days in this
     * span of time
     *
     * @return  int
     */
    public function getWholeDays()
    {
        return $this->getDays();
    }

    /**
     * Adds an amount of Days to this span of time
     *
     * @param int $days
     */
    public function addDays($days): void
    {
        $this->seconds += (int)$days * 86400;
    }

    /**
     * Format timespan
     *
     * Format tokens are:
     * <pre>
     * %s   - seconds
     * %w   - 'whole' seconds
     * %m   - minutes
     * %M   - minutes (float)
     * %j   - 'whole' minutes
     * %h   - hours
     * %H   - hours (float)
     * %y   - 'whole' hours
     * %d   - days
     * %D   - days (float)
     * %e   - 'whole' days
     * </pre>
     *
     * @param   string $format
     *
     * @return  string the formatted timespan
     */
    public function format($format): string
    {
        $return = '';
        for ($i = 0, $len = strlen($format); $i < $len; $i++) {
            if ($format[$i] == '%' && ($i + 1) < $len) {
                match ($format[$i + 1]) {
                    's' => $return .= $this->getSeconds(),
                    'w' => $return .= $this->getWholeSeconds(),
                    'm' => $return .= $this->getMinutes(),
                    'M' => $return .= sprintf('%.2f', $this->getMinutesFloat()),
                    'j' => $return .= $this->getWholeMinutes(),
                    'h' => $return .= $this->getHours(),
                    'H' => $return .= sprintf('%.2f', $this->getHoursFloat()),
                    'y' => $return .= $this->getWholeHours(),
                    'd' => $return .= $this->getDays(),
                    'D' => $return .= sprintf('%.2f', $this->getDaysFloat()),
                    'e' => $return .= $this->getWholeDays(),
                    '%' => $return .= '%',
                    default => $i--
                };
                $i++;
            } else {
                $return .= $format[$i];
            }
        }

        return $return;
    }

    /**
     * Indicates whether the timespan to compare equals this timespan
     *
     * @param TimeSpan $cmp
     *
     * @return bool true if the two timespan objects are equal
     */
    public function equals($cmp): bool
    {
        return ($cmp instanceof TimeSpan) && ($cmp->getSeconds() == $this->getSeconds());
    }

    /**
     * Creates a string representation
     *
     * @param string $format, defaults to '%ed, %yh, %jm, %ws'
     *
     * @return string
     */
    public function toString($format = '%ed, %yh, %jm, %ws')
    {
        return $this->format($format);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
