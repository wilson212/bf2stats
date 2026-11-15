<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */

/**
 * ArgumentException
 *
 * @author      Steven Wilson
 * @package     System
 * @subpackage  Exceptions
 */
class ArgumentException extends Exception
{
    public function __construct($message, protected $argument = '', $inner = null)
    {
        parent::__construct($message, 0, $inner);
    }

    public function getArgument()
    {
        return $this->argument;
    }
}