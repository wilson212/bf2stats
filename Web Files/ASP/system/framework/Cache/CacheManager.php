<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
namespace System\Cache;
use Exception;
use ReflectionClass;
use System\Autoloader;
use System\Collections\Dictionary;

/**
 * Class CacheManager
 * @package System\Cache
 */
class CacheManager
{
    /**
     * @var Dictionary
     */
    protected static $Cache;

    /**
     * Fetches the Cache Instance by name
     *
     *
     * @return ICacheDriver
     * @throws Exception
     */
    public static function GetInstance(string $name)
    {
        // Ensure the cache dictionary is set
        if (is_null(self::$Cache)) {
            self::$Cache = new Dictionary(false);
        }

        // Check if driver is already loaded
        if (self::$Cache->containsKey($name)) {
            return self::$Cache[$name];
        }

        // Load driver file
        $className = 'System\Cache\Drivers\\' . $name;
        if (!Autoloader::LoadClass($className))
        {
            throw new Exception("Unable to located Cache Driver: ". $className);
        }

        // Load the controller reflection
        $class = new ReflectionClass($className);

        // Ensure it inherits the ICacheDriver interface
        if (!$class->implementsInterface(ICacheDriver::class))
        {
            throw new Exception(sprintf("Cache Driver (%s) Does not implement the ICacheDriver interface", $className));
        }

        // Create instance
        $class = new $className();

        // Score for later
        self::$Cache->add($name, $class);
        return $class;
    }
}