<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
namespace System\BF2;

use ArrayAccess;
use ArgumentOutOfRangeException;

/**
 * Class ObjectStat
 *
 * @package System\BF2
 */
class ObjectStat implements ArrayAccess
{
    /**
     * @var int The object id
     */
    public $id = 0;

    /**
     * @var int The time in seconds played with this object
     */
    public $time = 0;

    /**
     * @var int The total score earned with this object
     */
    public $score = 0;

    /**
     * @var int The number of kills with this object
     */
    public $kills = 0;

    /**
     * @var int The number of deaths with this object
     */
    public $deaths = 0;

    /**
     * @var int The number of shots fired with this object
     */
    public $fired = 0;

    /**
     * @var int The number of hits with this object
     */
    public $hits = 0;

    /**
     * @var int The number of road kills with this object
     */
    public $roadKills = 0;

    /**
     * @var int The number of times this object was deployed
     */
    public $deployed = 0;

    /**
     * Whether a offset exists
     *
     * @param string $offset An offset to check for.
     *
     * @return boolean true on success or false on failure.
     *
     * The return value will be casted to boolean if non-boolean was returned.
     *
     */
    public function offsetExists($offset): bool
    {
        return property_exists($this, $offset);
    }

    /**
     * Offset to retrieve
     *
     * @param string $offset The offset to retrieve.
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset): mixed
    {
        if (!$this->offsetExists($offset))
        {
            throw new ArgumentOutOfRangeException("The given offset was not present in the object: {$offset}");
        }

        return $this->{$offset};
    }

    /**
     * Offset to set
     *
     * @param string $offset The offset to assign the value to.
     *
     * @param mixed $value The value to set.
     */
    public function offsetSet($offset, $value): void
    {
        if (!$this->offsetExists($offset))
        {
            throw new ArgumentOutOfRangeException("The given offset was not present in the object: {$offset}");
        }

        $this->{$offset} = $value;
    }

    /**
     * Method un-used
     *
     * @deprecated This method does not do anything
     *
     * @param string $offset The offset to unset.
     */
    public function offsetUnset($offset): void
    {

    }
}