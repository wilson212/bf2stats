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

/**
 * Class Debug
 * @package System
 */
class Debug
{
    public static function Dump($item): never
    {
        die('<pre>'. var_export($item, true) . "</pre>");
    }
}