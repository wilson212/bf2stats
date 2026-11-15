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

use InvalidArgumentException;

/**
 * Represents a version number
 *
 * @package System
 */
class Version
{
    /**
     * @var int The integer representation of this version
     */
    protected $intVal = 0;

    /**
     * Version constructor.
     *
     * @param int $major The major component of the version number
     * @param int $minor The minor component of the version number
     * @param int $revision The revision component of the version number
     */
    public function __construct(public $major, public $minor = 0, public $revision = 0)
    {
    }

    /**
     * Converts the string representation of a version number to an equivalent Version object.
     *
     * @param string $version A string that contains a version number to convert.
     */
    public static function Parse($version): \System\Version
    {
        // Ensure valid characters are passed
        if (!preg_match("/[0-9.]+/i", $version)) {
            throw new InvalidArgumentException("Version string contains illegal characters");
        }

        $ver_arr = explode(".", $version);
        $size = count($ver_arr);
        if ($size >= 3)
        {
            $major = (int)$ver_arr[0];
            $minor = (int)$ver_arr[1];
            $rev = (int)$ver_arr[2];

            return new Version($major, $minor, $rev);
        }
        elseif ($size == 2)
        {
            $major = (int)$ver_arr[0];
            $minor = (int)$ver_arr[1];

            return new Version($major, $minor, 0);
        }
        else {
            return new Version((int)$ver_arr[0]);
        }
    }

    /**
     * Compares 2 versions and returns which is greater
     *
     * @param string|Version $version The Version to compare to
     *
     * @return int Returns 0 if the versions are equal, -1 if the $version
     *   variable is larger then this instance, or 1 if this instance is
     *   greater then $version
     */
    public function compare($version): int
    {
        // Ensure we have a Version object before proceeding
        if (!($version instanceof Version)) {
            $version = Version::Parse($version);
        }

        // Versions are equal
        if ($version->toInt() == $this->toInt()) {
            return 0;
        } elseif ($version->toInt() > $this->toInt()) {
            return -1;
        } else {
            return 1;
        }
    }

    /**
     * Converts this Version object into a comparable integer.
     *
     * @return int
     */
    public function toInt()
    {
        if ($this->intVal == 0)
        {
            $this->intVal = $this->major * 10000;
            $this->intVal += $this->minor * 100;
            $this->intVal += $this->revision;
        }

        return $this->intVal;
    }

    public function toString(): string
    {
        return "{$this->major}.{$this->minor}.{$this->revision}";
    }

    /**
     * Determines whether the first specified Version object equals
     * the second specified Version object.
     *
     * @param Version|string $v1 The first version.
     * @param Version|string $v2 The second version
     */
    public static function Equals($v1, $v2): bool
    {
        // Ensure we have a Version object before proceeding
        if (!($v1 instanceof Version)) {
            $v1 = Version::Parse($v1);
        }

        // Ensure we have a Version object before proceeding
        if (!($v2 instanceof Version)) {
            $v2 = Version::Parse($v2);
        }

        return ($v1->toInt() == $v2->toInt());
    }

    /**
     * Determines whether the first specified Version object is greater
     * than the second specified Version object.
     *
     * @param Version|string $v1 The first version.
     * @param Version|string $v2 The second version
     */
    public static function GreaterThan($v1, $v2): bool
    {
        // Ensure we have a Version object before proceeding
        if (!($v1 instanceof Version)) {
            $v1 = Version::Parse($v1);
        }

        // Ensure we have a Version object before proceeding
        if (!($v2 instanceof Version)) {
            $v2 = Version::Parse($v2);
        }

        return ($v1->toInt() > $v2->toInt());
    }

    /**
     * Determines whether the first specified Version object is greater
     * than or equal to the second specified Version object.
     *
     * @param Version|string $v1 The first version.
     * @param Version|string $v2 The second version
     */
    public static function GreaterThanOrEqual($v1, $v2): bool
    {
        // Ensure we have a Version object before proceeding
        if (!($v1 instanceof Version)) {
            $v1 = Version::Parse($v1);
        }

        // Ensure we have a Version object before proceeding
        if (!($v2 instanceof Version)) {
            $v2 = Version::Parse($v2);
        }

        return ($v1->toInt() >= $v2->toInt());
    }

    /**
     * Determines whether the first specified Version object is less
     * than the second specified Version object.
     *
     * @param Version|string $v1 The first version.
     * @param Version|string $v2 The second version
     */
    public static function LessThan($v1, $v2): bool
    {
        // Ensure we have a Version object before proceeding
        if (!($v1 instanceof Version)) {
            $v1 = Version::Parse($v1);
        }

        // Ensure we have a Version object before proceeding
        if (!($v2 instanceof Version)) {
            $v2 = Version::Parse($v2);
        }

        return ($v1->toInt() < $v2->toInt());
    }

    /**
     * Determines whether the first specified Version object is less
     * than or equal to the second specified Version object.
     *
     * @param Version|string $v1 The first version.
     * @param Version|string $v2 The second version
     */
    public static function LessThanOrEqual($v1, $v2): bool
    {
        // Ensure we have a Version object before proceeding
        if (!($v1 instanceof Version)) {
            $v1 = Version::Parse($v1);
        }

        // Ensure we have a Version object before proceeding
        if (!($v2 instanceof Version)) {
            $v2 = Version::Parse($v2);
        }

        return ($v1->toInt() <= $v2->toInt());
    }
}