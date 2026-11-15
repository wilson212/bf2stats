<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
namespace System\IO;

/**
 * Performs operations on String instances that contain file or directory path information.
 * These operations are performed in a cross-platform manner.
 *
 * @package System\IO
 */
class Path
{
    /**
     * Changes the extension of a path string
     *
     * @param string $path The path information to modify
     * @param string $extension The new extension. Leave null to remove extension
     *
     * @return string Returns the full path using the correct system
     *   directory separator
     */
    public static function ChangeExtension(string $path, $extension): string
    {
        // If the path has an extension, change it
        if (($pos = strripos($path, '.')) !== false)
        {
            $parts = substr($path, 0, $pos);

            return (empty($extension)) ? $parts : $parts . '.' . ltrim($extension, '.');
        }
        else
        {
            // Add extension
            return (empty($extension)) ? $path : $path . '.' . ltrim($extension, '.');
        }
    }

    /**
     * Combines several string arguments into a file path.
     *
     * Each argument passed represent pieces of the path, and can be a
     * single dimensional array of paths, a string folder / filename, or a mixture
     * of the two. Dots may also be passed ( . & .. ) to change directory levels
     *
     * @return string Returns the full path using the correct system
     *   directory separator
     *
     */
    public static function Combine(): string
    {
        // Get our path parts
        $args = func_get_args();
        $parts = [];

        // Trim our paths to remove spaces and new lines
        foreach ($args as $part)
        {
            // If part is array, then implode and continue
            if (is_array($part))
            {
                // Remove empty entries
                $part = array_filter($part, 'strlen');
                $parts[] = implode(DIRECTORY_SEPARATOR, $part);
                continue;
            }

            // String
            $part = trim((string) $part);
            if ($part === '.' || ($part === '' || $part === '0')) {
                continue;
            } elseif ($part === '..') {
                array_pop($parts);
            } else {
                $parts[] = $part;
            }
        }

        // Get our cleaned path into a variable with the correct directory separator
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    /**
     * Returns the directory name for the specified path string.
     *
     * @param string $path The path we are getting the directory name for
     *
     * @return string Returns the full path using the correct system
     *   directory separator
     */
    public static function GetDirectoryName($path): string
    {
        return dirname($path);
    }

    /**
     * Returns the extension of the specified path string.
     *
     * @param string $path The file path we are getting the extension for
     */
    public static function GetExtension($path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Returns the file name and extension of the specified path string
     *
     * @param string $path The file path we are getting the name of
     */
    public static function GetFilename($path): string
    {
        return basename($path);
    }

    /**
     * Returns the file name of the specified path string without the extension
     *
     * @param string $path The file path we are getting the name of
     */
    public static function GetFilenameWithoutExtension($path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }
}