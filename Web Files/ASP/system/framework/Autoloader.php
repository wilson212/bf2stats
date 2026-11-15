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
 * This class is an advanced autoloader for missing class references.
 *
 * @author      Steven Wilson
 * @package     System
 */
class Autoloader
{
    /**
     * A bool that states whether the Autoloader is registered with spl_autoload
     * @var bool
     */
    protected static $isRegistered = false;

    /**
     * An array of registered paths
     * @var string[]
     */
    protected static $paths = [];

    /**
     * An array of registered namespace => path
     * @var array
     */
    protected static $namespaces = [];

    /**
     * Registers the AutoLoader class with spl_autoload. Multiple
     * calls to this method will not yield any additional results.
     */
    public static function Register(): void
    {
        if (self::$isRegistered) {
            return;
        }

        spl_autoload_register('System\Autoloader::LoadClass');

        // Add paths for the system
        self::$namespaces['System'] = [__DIR__];
        self::$paths[0] = __DIR__ . DIRECTORY_SEPARATOR . "Exceptions";

        self::$isRegistered = true;
    }

    /**
     * Un-Registers the AutoLoader class with spl_autoload
     */
    public static function UnRegister(): void
    {
        if (!self::$isRegistered) {
            return;
        }

        spl_autoload_unregister('System\Autoloader::LoadClass');

        self::$isRegistered = false;
    }

    /**
     * Registers a path for the autoload to search for classes. Namespaced
     * and prefixed registered paths will be searched first if the class
     * is namespaced, or prefixed.
     *
     * @param string $path Full path to search for a class
     */
    public static function RegisterPath($path): void
    {
        if (!in_array($path, self::$paths)) {
            self::$paths[] = $path;
        }
    }

    /**
     * Registers a path for the autoloader to search in when searching
     * for a specific namespaced class. When calling this method more
     * than once with the same namespace, the path(s) will just be added
     * to the current running list of paths for that namespace
     *
     * @param string $namespace The namespace we are registering
     * @param string|array $path Full path, or an array of paths
     *   to search for the namespaced class'.
     */
    public static function RegisterNamespace($namespace, $path): void
    {
        // Make sure path is array
        if (!is_array($path)) {
            $path = [$path];
        }

        // Fix path, providing correct directory separator
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);

        // Set namespace paths
        if (isset(self::$namespaces[$namespace])) {
            self::$namespaces[$namespace] = array_merge(self::$namespaces[$namespace], $path);
        } else {
            self::$namespaces[$namespace] = $path;
        }
    }

    /**
     * Returns an array of all registered namespaces as keys, and an array
     * of registered paths for that namespace as values
     *
     * @return string[]
     */
    public static function GetNamespaces()
    {
        return self::$namespaces;
    }

    /**
     * Method used to search all registered paths for a missing class
     * reference (used by the spl_autoload method)
     *
     * @param string $class The class being loaded
     *
     * @return Bool Returns TRUE if the class is found, and file was
     *   included successfully.
     */
    public static function LoadClass($class): bool
    {
        // Check for namespaced class
        $parts = explode('\\', trim($class, '\\'));
        $length = count($parts);

        // If the class name is namespaced, we will use the namespace to determine
        // the path to the class file.
        if ($length > 1)
        {
            // Remove class name from parts, and store it here
            $class = array_pop($parts);
            $namespace = implode('\\', $parts);

            /**
             * we will keep checking all namespaces, working up 1 level
             * each time until we reach a defined namespace path, and from there,
             * each sub namespace we removed becomes a sub dir to the class file path.
             */
            for ($i = $length; $i >= 0; $i--)
            {
                // Check if namespace is set
                if (isset(self::$namespaces[$namespace]))
                {
                    foreach (self::$namespaces[$namespace] as $dir)
                    {
                        // Build full directory path
                        $file = $dir . DIRECTORY_SEPARATOR . $class . '.php';

                        // Check if class file exists
                        if (file_exists($file))
                        {
                            /** @noinspection PhpIncludeInspection */
                            require $file;
                            return true;
                        }
                    }
                    break;
                }
                else
                {
                    // Prepend the last namespace part to the class name, and try the parent namespace
                    $class = array_pop($parts) . DIRECTORY_SEPARATOR . $class;
                    $namespace = implode('\\', $parts);
                }
            }
        }

        // If all else fails (no namespace was found), check default registered paths
        foreach (self::$paths as $dir)
        {
            $class = str_replace(['_', '\\', '/'], DIRECTORY_SEPARATOR, $class);
            $file = $dir . DIRECTORY_SEPARATOR . $class . '.php';
            if (file_exists($file))
            {
                /** @noinspection PhpIncludeInspection */
                require $file;
                return true;
            }
        }

        // If we are here, we didn't find the class :(
        return false;
    }
}
// EOF