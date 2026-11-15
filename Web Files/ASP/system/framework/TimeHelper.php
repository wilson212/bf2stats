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

use DateTime;

/**
 * Class TimeHelper
 * @package System
 */
class TimeHelper
{
    /**
     * Converts a time from seconds to a string format of h:m:s
     *
     * @param $seconds
     */
    public static function SecondsToHms($seconds): string
    {
        $h = floor($seconds / 3600);
        $reste_secondes = $seconds - $h * 3600;

        $m = floor($reste_secondes / 60);
        $reste_secondes -= $m * 60;

        $s = round($reste_secondes, 3);
        $s = number_format($s, 0, '.', '');

        $h = str_pad($h, 2, '0', STR_PAD_LEFT);
        $m = str_pad($m, 2, '0', STR_PAD_LEFT);
        $s = str_pad($s, 2, '0', STR_PAD_LEFT);

        return $h . ":" . $m . ":" . $s;
    }

    /**
     * Formats a time difference (timestamp) into a human readable format
     *
     * @param int $time1 The earliest time
     * @param int $time2 The latter time
     * @param int $length The number of interval parts to display
     */
    public static function FormatDifference($time1, $time2, $length = 2): string
    {
        if ($time1 == 0) {
            return 'Never';
        }

        // Define variables
        $now = new DateTime("@". $time2);
        $last = new DateTime("@". $time1);
        $interval = $now->diff($last);
        $parts = [];

        // Append year difference
        if ($interval->y > 0)
        {
            $parts[] = $interval->y . (($interval->y > 1) ? ' years' : ' year');
        }

        // Append month difference
        if ($interval->m > 0)
        {
            $parts[] = $interval->m . (($interval->m > 1) ? ' months' : ' month');
        }

        // Append day difference
        if ($interval->d > 0)
        {
            $parts[] = $interval->d . (($interval->d > 1) ? ' days' : ' day');
        }

        // Append hour difference
        if ($interval->h > 0)
        {
            $parts[] = $interval->h . (($interval->h > 1) ? ' hours' : ' hour');
        }

        // Append minute difference
        if ($interval->i > 0)
        {
            $parts[] = $interval->i . (($interval->i > 1) ? ' minutes' : ' minute');
        }

        // Append second difference
        if ($interval->s > 0)
        {
            $parts[] = $interval->s . (($interval->s > 1) ? ' seconds' : ' second');
        }

        return implode(', ', array_slice($parts, 0, $length, true)) . " ago";
    }
}