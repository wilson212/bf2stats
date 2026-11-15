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
use FileNotFoundException;
use IOException;
use InvalidArgumentException;

/**
 * Provides static methods for various file operations
 *
 * @author      Steven Wilson
 * @package     System
 * @subpackage  IO
 */
class File
{
    /**
     * Creates a new file to the path specified
     *
     * @param string $path The full file path, including filename, of the
     *        file we are creating
     * @param bool $returnStream Return the FileStream for reading/writing?
     *
     * @throws IOException Thrown this method is unable to create the file
     *
     * @return FileStream
     */
    public static function Create($path, $returnStream = false): ?FileStream
    {
        $Stream = new FileStream($path);
        if ($returnStream) {
            return $Stream;
        }

        $Stream->close();

        return null;
    }

    /**
     * Returns whether a file path exists or not.
     *
     * @param string $path The full file path, including filename, of the
     *        file we are checking for
     */
    public static function Exists($path): bool
    {
        return file_exists($path);
    }

    /**
     * Removes a file from the filesystem
     *
     * @param string $path The full file path, including filename, of the
     *        file we are removing
     *
     * @return bool
     */
    public static function Delete($path)
    {
        return @unlink($path);
    }

    /**
     * Opens a FileStream on the specified path with read/write access
     *
     * @param string $path The full path, including file name to the file.
     *
     * @throws FileNotFoundException Thrown if the file does not exist
     * @throws IOException Thrown if there was an error opening the file.
     */
    public static function Open($path): FileStream
    {
        return new FileStream($path, FileStream::READWRITE);
    }

    /**
     * Opens a FileStream on the specified path with write access
     *
     * @param string $filePath The full path, including file name to the file.
     *
     * @throws FileNotFoundException Thrown if the file does not exist
     * @throws IOException Thrown if there was an error opening the file.
     */
    public static function OpenWrite($filePath): FileStream
    {
        return new FileStream($filePath, FileStream::WRITE);
    }

    /**
     * Opens a FileStream on the specified path with read access
     *
     * @param string $filePath The full path, including file name to the file.
     *
     * @throws FileNotFoundException Thrown if the file does not exist
     * @throws IOException Thrown if there was an error opening the file.
     */
    public static function OpenRead($filePath): FileStream
    {
        return new FileStream($filePath, FileStream::READ);
    }

    /**
     * Appends lines to a file
     *
     * If the specified file does not exist, this method creates a file,
     * and writes the specified lines to the file.
     *
     * @param string $filePath The full path, including file name to the file.
     * @param string[] $lines An array of lines to write to the file.
     *
     * @throws IOException Thrown if there was an error opening, or creating the file.
     * @throws InvalidArgumentException Thrown if $lines is not an array, or ListObject
     *
     * @return bool Returns whether the operation was successful
     */
    public static function AppendAllLines($filePath, array $lines)
    {
        if (!is_array($lines)) {
            throw new InvalidArgumentException("Second parameter must be an array, " . gettype($lines) . " given");
        }

        return self::AppendAllText($filePath, implode(PHP_EOL, $lines));
    }

    /**
     * Appends string data to a file
     *
     * If the specified file does not exist, this method creates a file,
     * and writes the specified lines to the file.
     *
     * @param string $filePath The full path, including file name to the file.
     * @param string $stringData The data string to write to the file
     *
     * @throws IOException Thrown if there was an error opening, or creating the file.
     *
     * @return bool Returns whether the operation was successful
     */
    public static function AppendAllText($filePath, $stringData): bool
    {
        // Get filestream
        $file = new FileStream($filePath, FileStream::WRITE);

        // Write file contents
        $wrote = $file->write($stringData);
        $file->close();

        return $wrote !== false;
    }

    /**
     * Opens a file, and gets all the lines of the file
     *
     * @param string $filePath The full path, including file name to the file.
     *
     * @throws FileNotFoundException Thrown if the file does not exist
     * @throws IOException Thrown if there was an error opening the file.
     *
     * @return string[]
     */
    public static function ReadAllLines($filePath)
    {
        return explode("\n", str_replace("\r\n", "\n", self::ReadAllText($filePath)));
    }

    /**
     * Opens a file, and gets all data of the file
     *
     * @param string $filePath The full path, including file name to the file.
     *
     * @throws FileNotFoundException Thrown if the file does not exist
     * @throws IOException Thrown if there was an error opening the file.
     *
     * @return string
     */
    public static function ReadAllText($filePath)
    {
        // Ensure the file exists
        if (!file_exists($filePath)) {
            throw new FileNotFoundException("File \"{$filePath}\" does not exist");
        }

        // Read the contents from the file
        $file = new FileStream($filePath, FileStream::READ);
        $contents = $file->readToEnd();
        $file->close();

        // Return the file contents
        return $contents;
    }

    /**
     * Creates or overwrites a file, amd writes the specified string array to the file
     *
     * @param string $filePath The full path, including file name to the file.
     * @param string[] $lines An array of lines to write to the file.
     *
     * @throws IOException Thrown if there was an error opening, or creating the file.
     * @throws InvalidArgumentException Thrown if $lines is not an array, or ListObject
     *
     * @return bool Returns whether the operation was successful
     */
    public static function WriteAllLines($filePath, array $lines)
    {
        if (!is_array($lines)) {
            throw new InvalidArgumentException("Second parameter must be an array, " . gettype($lines) . " given");
        }

        return self::WriteAllText($filePath, implode(PHP_EOL, $lines));
    }

    /**
     * Creates or overwrites a file, amd writes the specified string to the file
     *
     * @param string $filePath The full path, including file name to the file.
     * @param string $stringData The data string to write to the file
     *
     * @return bool Thrown if there was an error opening, or creating the file.
     *
     * @throws IOException Thrown if there was an error opening, or creating the file.
     */
    public static function WriteAllText($filePath, $stringData): bool
    {
        // Get file stream
        $file = new FileStream($filePath, FileStream::WRITE);

        // Write file contents
        $file->truncate();
        $wrote = $file->write($stringData);
        $file->close();

        return $wrote !== false;
    }

    /**
     * Moves a source file to a destination file. If the destination file already exists,
     * it will be overwritten.
     *
     * @param string $source The full file path, including filename, of the
     *        file we are moving
     * @param string $destination The full file path, including filename, of the
     *        file that will be created or overwritten.
     *
     * @throws InvalidArgumentException Thrown if any parameters are left null
     * @throws IOException Thrown if there was an error moving the file, or
     *     creating the destination file's directory if it did not exist
     */
    public static function Move($source, $destination): void
    {
        // Make sure we have a filename
        if (empty($source) || empty($destination)) {
            throw new InvalidArgumentException("Invalid file name passed");
        }

        // Correct new path
        $newPath = dirname($destination);

        // Make sure Dest directory exists
        if (!Directory::Exists($newPath)) {
            Directory::CreateDirectory($newPath, 0777);
        }

        // Create new file
        $file = new FileInfo($source);
        $file->moveTo($destination);
    }

    /**
     * Copies a source file to a destination file. If the destination file already exists,
     * it will be overwritten.
     *
     * @param string $source The full file path, including filename, of the
     *        file we are moving
     * @param string $destination The full file path, including filename, of the
     *        file that will be created or overwritten.
     *
     * @throws InvalidArgumentException Thrown if any parameters are left null
     * @throws IOException Thrown if there was an error copying the file, or
     *     creating the destination file's directory if it did not exist
     */
    public static function Copy($source, $destination): void
    {
        // Make sure we have a filename
        if (empty($source) || empty($destination)) {
            throw new InvalidArgumentException("Invalid file name passed");
        }

        // Correct new path
        $newPath = dirname($destination);

        // Make sure Dest directory exists
        if (!Directory::Exists($newPath)) {
            Directory::CreateDirectory($newPath, 0777);
        }

        // Create new file
        $file = new FileInfo($source);
        $file->copyTo($destination);
    }

    /**
     * Returns whether the specified file is writable or not.
     *
     * @param string $path The full path
     *
     * @return bool true if the file exists and is writable, false otherwise.
     */
    public static function IsWritable($path)
    {
        try
        {
            $file = new FileStream($path, 'r+');
            $canWrite = $file->canWrite();
            $file->close();
            return $canWrite;
        }
        catch (Exception)
        {
            return false;
        }
    }
}