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
use Exception;
use DirectoryNotFoundException;
use InvalidArgumentException;
use IOException;
use SecurityException;

/**
 * A Directory class used to preform advanced operations and provide information
 * about the directory.
 *
 * @author      Steven Wilson
 * @package     System
 * @subpackage  IO
 */
class Directory
{
    /**
     * Creates a new Directory to the specified path
     *
     * @param string $path The directory path
     * @param int $chmod The (octal) chmod permissions to assign this directory
     */
    public static function CreateDirectory($path, $chmod = 0755): bool
    {
        // If the directory exists, just return true
        if (is_dir($path)) {
            return true;
        }

        // Get current directory mask
        $oldumask = umask(0);
        if (@mkdir($path, $chmod, true) == false) {
            return false;
        }

        // Return to the old file mask, and return true
        umask($oldumask);
        return true;
    }

    /**
     * Deletes the specified directory and, if indicated, any subdirectories and files in the directory.
     *
     * @param string $path The full path of the directory to remove.
     * @param bool $recursive true to remove directories, subdirectories, and files in path; otherwise, false.
     *
     *
     * @throws DirectoryNotFoundException Path does not exist or could not be found.
     * @throws IOException An error occured while removing the directory
     */
    public static function Delete(string $path, $recursive = false): void
    {
        if ($recursive)
        {
            $dir = new DirectoryInfo($path);
            $dir->delete();
        }
        else
        {
            // Make sure the directory exists
            if (!is_dir($path)) {
                throw new DirectoryNotFoundException("Directory \"{$path}\" does not exist");
            }

            // Remove the directory
            $result = @rmdir($path);
            if ($result == false)
            {
                // Fetch and clear the last error
                $e = error_get_last();
                error_clear_last();

                // Throw an IOException to alert the user
                if ($e === null) {
                    throw new IOException('Could not remove directory: '. $path);
                } else {
                    throw new IOException('Could not remove directory: "'. $path .'". Exception thrown : '. $e['message']);
                }
            }
        }
    }

    /**
     * Returns whether a specified directory exists
     *
     * @param string $path The directory path
     */
    public static function Exists($path): bool
    {
        return is_dir($path);
    }

    /**
     * Gets the names of subdirectories (including their paths) in the specified directory
     *
     * @param string $path The directory path
     * @param string $searchPattern If defined, the sub-dir must match the specified search
     *     pattern in the specified directory in order to be returned in the list
     *
     * @throws DirectoryNotFoundException Thrown if the directory path doesn't exist
     * @throws SecurityException Thrown if the directory cant be opened because of permissions
     */
    public static function GetDirectories(string $path, $searchPattern = null): array
    {
        // Make sure the directory exists
        if (!is_dir($path)) {
            throw new DirectoryNotFoundException("Directory \"{$path}\" does not exist");
        }

        // Open the directory
        $handle = @opendir($path);
        if ($handle === false) {
            throw new SecurityException('Unable to open folder "' . $path . '"');
        }

        // Refresh vars
        $filelist = [];

        // Loop through each file
        while (false !== ($f = readdir($handle)))
        {
            // Skip self and parent directories
            if ($f === "." || $f === "..") {
                continue;
            }

            // make sure we establish the full path to the file again
            $file = Path::Combine($path, $f);

            // If is directory, call this method again to loop and delete ALL sub dirs.
            if (is_dir($file))
            {
                if (!empty($searchPattern))
                {
                    // If filename matches the regex, add to list
                    if (preg_match("/{$searchPattern}/i", $f)) {
                        $filelist[] = $file;
                    }
                }
                else {
                    $filelist[] = $file;
                }
            }
        }

        // Close our path
        closedir($handle);
        return $filelist;
    }

    /**
     * Returns the names of files (including their paths) in the specified directory.
     *
     * @param string $path The directory path
     * @param string $searchPattern If defined, the file must match the specified search
     *     pattern in the specified directory in order to be returned in the list
     *
     * @throws DirectoryNotFoundException Thrown if the directory path doesn't exist
     * @throws SecurityException Thrown if the directory cant be opened because of permissions
     */
    public static function GetFiles(string $path, $searchPattern = null): array
    {
        // Make sure the directory exists
        if (!is_dir($path)) {
            throw new DirectoryNotFoundException("Directory \"{$path}\" does not exist");
        }

        // Open the directory
        $handle = @opendir($path);
        if ($handle === false) {
            throw new SecurityException('Unable to open folder "' . $path . '"');
        }

        // Refresh vars
        $filelist = [];

        // Loop through each file
        while (false !== ($f = readdir($handle)))
        {
            // Skip self and parent directories
            if ($f === "." || $f === "..") {
                continue;
            }

            // make sure we establish the full path to the file again
            $file = Path::Combine($path, $f);

            // If is directory, call this method again to loop and delete ALL sub dirs.
            if (!is_dir($file))
            {
                if (!empty($searchPattern))
                {
                    // If filename matches the regex, add to list
                    if (preg_match("/{$searchPattern}/i", $f)) {
                        $filelist[] = $file;
                    }
                }
                else {
                    $filelist[] = $file;
                }
            }
        }

        // Close our path
        closedir($handle);
        return $filelist;
    }

    /**
     * Retrieves the parent directory of the specified path
     *
     * @param string $path The path for which to retrieve the parent directory.
     *
     * @throws DirectoryNotFoundException Thrown if the directory path doesn't exist
     * @throws SecurityException Thrown if the directory cant be opened because of permissions
     *
     * @return DirectoryInfo|null The parent directory, or null if path is the root directory
     */
    public static function GetParent($path): ?DirectoryInfo
    {
        $parent = dirname($path);
        return ($parent === DIRECTORY_SEPARATOR || $parent === ".") ? null : new DirectoryInfo($parent);
    }

    /**
     * Moves a directory and its contents to a new location
     *
     * This method will not merge two directories. If the Destination directory
     * exists, then an IOException will be thrown with an error code of 1. If you
     * require two directories be merged, then use the Directory::Merge() method.
     *
     * @param string $source The full file path, including filename, of the
     *        file we are moving
     * @param string $destination The full file path, including filename, of the
     *        file that will be created
     *
     * @throws DirectoryNotFoundException if the Source directory doesn't exist
     * @throws InvalidArgumentException Thrown if any parameters are left null
     * @throws IOException Thrown if there was an error creating the directory,
     *     or opening the destination directory after it was created, or if the
     *     destination directory already exists
     *
     * @return bool
     */
    public static function Move($source, $destination)
    {
        // Make sure we have a filename
        if (empty($source) || empty($destination)) {
            throw new InvalidArgumentException("Invalid file name passed");
        }

        // Make sure Dest directory exists
        if (!is_dir($source)) {
            throw new DirectoryNotFoundException("Source Directory \"{$source}\" does not exist.");
        }

        // Make sure Dest doesn't directory exist
        if (is_dir($destination)) {
            throw new IOException("Destination directory \"{$destination}\" already exists.", 1);
        }

        // Rename the directory
        return @rename($source, $destination);
    }

    /**
     * Merges a source directory into a destination directory
     *
     * If the Destination directory does not exist, this method will attempt to create it.
     * The source directory must exist! After the operation, only the Destination directory
     * will remain, and the source directory will be removed.
     *
     * @param string $source The full file path of the source directory
     * @param string $destination The full file path of the destination directory
     * @param bool $overwrite Indicates whether files from the source directory
     *     will overwrite files of the same name in the destination folder
     *
     * @throws InvalidArgumentException Thrown if any parameters are left null
     * @throws IOException Thrown if there was an error creating the directory,
     *     or opening the destination directory after it was created, or if there
     *     an error moving over a file or directory to the destination directory
     */
    public static function Merge($source, $destination, $overwrite = true): void
    {
        // Make sure we have a filename
        if (empty($source) || empty($destination)) {
            throw new InvalidArgumentException("Invalid file name passed");
        }

        // Make sure Dest directory exists
        $Source = new DirectoryInfo($source);
        $Dest = new DirectoryInfo($destination, true);

        // Create source sub directories in the destination directory
        foreach ($Source->getDirectories() as $Dir)
        {
            self::Merge(
                Path::Combine($source, $Dir->name()),
                Path::Combine($destination, $Dir->name()),
                $overwrite
            );
        }

        // Copy over files
        foreach ($Source->getFiles() as $File)
        {
            $destFileName = Path::Combine($destination, $File->name());
            if (!$overwrite && in_array($destFileName, $Dest->getFiles())) {
                continue;
            }

            $File->moveTo($destFileName);
        }

        // Remove the source directory
        @rmdir($source);
    }

    /**
     * Returns whether the specified directory is writable or not.
     *
     * @param string $path The full path
     *
     * @return bool true if the directory exists and is writable, false otherwise.
     */
    public static function IsWritable($path)
    {
        try
        {
            $dir = new DirectoryInfo($path, false);
            return $dir->isWritable();
        }
        catch (Exception)
        {
            return false;
        }
    }
}