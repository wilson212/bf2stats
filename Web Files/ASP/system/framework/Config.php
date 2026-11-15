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
use Exception;
use System\IO\File;

/**
 * Class Config
 * @package System
 */
class Config
{
    /**
     * @var array
     */
    protected static $data = [];

    /**
     * @var string
     */
    protected static $configFile;

    /**
     * Constructor. Loads the configuration file
     * @throws Exception
     */
    public static function Init(): void
    {
        if (empty(self::$configFile))
        {
            // Load the config File
            self::$configFile = SYSTEM_PATH . DS . 'config' . DS . 'config.php';
            if (!self::Load()) {
                throw new Exception('Failed to load config file!');
            }
        }
    }

    /**
     * Returns the variable ($key) value in the config file.
     *
     * @param string $key - variable name. Value is returned
     *
     * @return mixed|null
     */
    public static function Get($key)
    {
        // Check if the variable exists
        return self::$data[$key] ?? null;
    }

    /**
     * Returns the variable ($key) value in the config file.
     *
     * @param string $key variable name. Value is returned
     * @param mixed $default the default value to return if the key doesn't exist
     *
     * @return mixed
     */
    public static function GetOrDefault($key, $default)
    {
        // Check if the variable exists
        return (array_key_exists($key, self::$data)) ? self::$data[$key] : $default;
    }

    /**
     * Returns all variable keys and values in the config file.
     *
     * @return array
     */
    public static function FetchAll()
    {
        return self::$data;
    }

    /**
     * Sets new value for a config variable
     *
     * @param string|array $key variable name to be set, or an array of key => value
     * @param bool $val new value of the variable
     */
    public static function Set($key, $val = false): void
    {
        // If we have array, loop through and set each
        if (is_array($key))
        {
            foreach ($key as $k => $v)
                self::$data[$k] = $v;
        }
        else
        {
            self::$data[$key] = $val;
        }
    }

    /**
     * Saves all set config variables to the config file, and makes
     * a backup of the current config file
     *
     * @return bool true on success, false otherwise
     */
    public static function Save()
    {
        $cfg = "<?php\n";
        $cfg .= "/***************************************\n";
        $cfg .= "*  Battlefield 2 Private Stats Config  *\n";
        $cfg .= "****************************************\n";
        $cfg .= "* All comments have been removed from  *\n";
        $cfg .= "* this file. Please use the Web Admin  *\n";
        $cfg .= "* to change values.                    *\n";
        $cfg .= "***************************************/\n";

        // Get each of the new set variables
        foreach (self::$data as $key => $val)
        {
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/D', $key)) {
                throw new Exception('Invalid configuration key');
            }
            if (in_array($key, ['admin_hosts', 'game_hosts', 'stats_local_pids', 'battlespy_weapons'], true)
                && !is_array($val)) {
                $val = array_map('trim', explode("\n", (string)$val));
            }
            // Preserve numeric passwords (including leading zeroes), and escape
            // array elements as PHP literals rather than executable source.
            $cfg .= '$' . $key . ' = ' . var_export($val, true) . ";\n";
        }

        // Copy the current config file for backup
        File::Copy(self::$configFile, self::$configFile . '.bak');

        // Allow the file to move before starting a new IO operation
        // This was on Issue on a Windows 10 machine using Wamp
        sleep(1);

        // Write the new config values to the new config
        return File::WriteAllText(self::$configFile, $cfg);
    }

    /**
     * Load the config file, and adds its defined variables to the internal data array
     */
    protected static function Load(): bool
    {
        if (file_exists(self::$configFile))
        {
            /** @noinspection PhpIncludeInspection */
            include_once(self::$configFile);
            $vars = get_defined_vars();
            foreach ($vars as $key => $val)
            {
                if ($key !== 'this' && $key !== 'data') {
                    self::$data[$key] = $val;
                }
            }

            return true;
        }

        return false;
    }
}

Config::Init();
