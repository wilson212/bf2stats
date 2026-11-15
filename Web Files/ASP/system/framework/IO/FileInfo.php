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
use Stringable;
use COM;
use SecurityException;
use DirectoryNotFoundException;
use FileNotFoundException;
use IOException;
use Exception;

/**
 * Provides properties and instance methods for various file operations
 *
 * Use the FileInfo class if you are going to reuse an object several times,
 * because a file exists check will not always be necessary, and will increase
 * performance over the static File class methods
 *
 * @author      Steven Wilson
 * @package     System
 * @subpackage  IO
 */
class FileInfo implements Stringable
{
    /**
     * The full path to the file's parent directory
     */
    protected string $parentDir;

    /**
     * The full path to the file's current location, including the filename
     * @var string
     */
    protected $filePath;

    /**
     * Class Constructor
     *
     * @param string $path The full path the the file
     * @param bool $create Create the file if it doesn't exist?
     *
     * @throws IOException Thrown if the $path directory doesn't exist,
     *   $create is set to true, and there was an error creating the file.
     * @throws FileNotFoundException If the $path file does not exist, and $create is set to false.
     * @throws Exception Thrown if the $path is not a file at all, but rather a directory
     */
    public function __construct($path, $create = false)
    {
        // Make sure the file exists, or we are creating a file
        if (!file_exists($path))
        {
            // Do we attempt to create?
            if (!$create) {
                throw new FileNotFoundException("File '{$path}' does not exist");
            }

            // Attempt to create the file
            $handle = @fopen($path, 'w+');
            if ($handle)
            {
                // Close the handle
                fclose($handle);
            }
            else {
                throw new IOException("Cannot create file '{$path}'");
            }
        }
        elseif (!is_file($path))
        {
            throw new Exception("'{$path}' is not a file!");
        }

        // Define path
        $this->filePath = $path;
        $this->parentDir = dirname($path);
    }

    /**
     * Returns the base file name
     */
    public function name(): string
    {
        return basename($this->filePath);
    }

    /**
     * Returns the full path to the file, including the file name
     *
     * @return string
     */
    public function fullName()
    {
        return $this->filePath;
    }

    /**
     * Returns the the extension part of the file
     */
    public function extension(): string
    {
        return pathinfo($this->filePath, PATHINFO_EXTENSION);
    }

    /**
     * Returns the files 's directory path
     *
     * @return string
     */
    public function directoryName()
    {
        return $this->parentDir;
    }

    /**
     * Gets an instance of the parent directory
     */
    public function directory(): DirectoryInfo
    {
        return new DirectoryInfo($this->parentDir);
    }

    /**
     * Appends the specified string to the file
     *
     * @param string $stringData The data string to write to the file
     *
     * @throws IOException Thrown if there was an error opening, or writing to the file.
     *
     * @return bool Returns whether the operation was successful
     */
    public function appendText($stringData): bool
    {
        $File = new FileStream($this->filePath, FileStream::WRITE);
        $wrote = $File->write($stringData);
        $File->close();

        return $wrote !== false;
    }

    /**
     * Opens a FileStream on the specified path with read/write access
     *
     * @throws IOException Thrown if there was an error opening the file.
     */
    public function open(): FileStream
    {
        return new FileStream($this->filePath, FileStream::READWRITE);
    }

    /**
     * Opens a FileStream on the specified path with read access
     *
     * @throws IOException Thrown if there was an error opening the file.
     */
    public function openRead(): FileStream
    {
        return new FileStream($this->filePath, FileStream::READ);
    }

    /**
     * Opens a FileStream on the specified path with write access
     *
     * @throws IOException Thrown if there was an error opening the file.
     */
    public function openWrite(): FileStream
    {
        return new FileStream($this->filePath, FileStream::WRITE);
    }

    /**
     * Moves the file to a new location.
     *
     * The old file will not be removed until the new file is created successfully.
     *
     * @param string $newPath The full file name (including full path) to move to
     *
     * @throws IOException Thrown if there was an error creating or writing to the new file
     * @throws SecurityException Thrown if the $newPath directory could not be opened
     *   for various security reasons such as permissions.
     * @throws DirectoryNotFoundException if the directory specified in fileName does not exist.
     */
    public function moveTo($newPath): void
    {
        // Copy this file's contents to the new
        $this->copyTo($newPath, true);

        // Delete old file
        @unlink($this->filePath);

        // Reset class vars
        $this->filePath = $newPath;
        $this->parentDir = dirname($newPath);
    }

    /**
     * Copies the contents of this file to a new file
     *
     * @param string $fileName The name of the file we are copying to
     * @param bool $overwrite Defines whether to overwrite an existing
     *     file, if it exists
     *
     * @throws DirectoryNotFoundException if the directory specified in fileName does not exist.
     *
     * @return bool Returns true on success, false otherwise
     */
    public function copyTo($fileName, $overwrite = false)
    {
        // If file exists, and we disallow overwriting
        if (!$overwrite && file_exists($fileName)) {
            return false;
        }

        // Ensure directory exists
        $dir = Path::GetDirectoryName($fileName);
        if (!Directory::Exists($dir)) {
            throw new DirectoryNotFoundException("Could not find part of path: ". $dir);
        }

        // Grab the contents from this file
        $stream = $this->openRead();
        $contents = $stream->readToEnd();
        $stream->close();

        // Create the new file, or truncate it to zero if it already exists
        $file = new FileStream($fileName, 'w');
        $bytesWritten = $file->write($contents);
        $file->close();

        // return the copy result
        return $bytesWritten > 0;
    }

    /**
     * Completely removes all contents of the file
     *
     * @return bool Returns true on success, false otherwise
     */
    public function truncate(): bool
    {
        $f = @fopen($this->filePath, "r+");
        if ($f !== false)
        {
            ftruncate($f, 0);
            fclose($f);

            return true;
        }

        return false;
    }

    /**
     * Gets last modification time of file
     *
     * @return int|bool Returns the time the file was last modified,
     * or FALSE on failure. The time is returned as a Unix timestamp.
     */
    public function lastWriteTime(): int|false
    {
        return filemtime($this->filePath);
    }

    /**
     * Gets last access time of file
     *
     * @return int|bool Returns the time the file was last accessed,
     * or FALSE on failure. The time is returned as a Unix timestamp.
     */
    public function lastAccessTime(): int|false
    {
        return fileatime($this->filePath);
    }

    /**
     * Gets the size,  in bytes, of the current file
     * @TODO Fix Me
     * @return int
     */
    public function size()
    {
        // Get most accurate file size based on operating system
        $total_size = 0;
        $is64Bit = (PHP_INT_SIZE > 4);

        // If we suspect the file being over 2 GB on a 32 bit system, use command line
        if (!$is64Bit)
        {
            // Get file size
            $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
            $total_size = ($isWindows)
                ? exec("for %v in (\"" . $this->filePath . "\") do @echo %~zv") // Windows
                : shell_exec("stat -c%s " . escapeshellarg($this->filePath)); // Linux

            // If we failed to get a size, we take extreme measures
            if (!is_numeric($total_size) || $total_size == 0)
            {
                if ($isWindows)
                {
                    // Check for windows COM
                    if (class_exists("COM", false))
                    {
                        $fsobj = new COM('Scripting.FileSystemObject');
                        $f = $fsobj->GetFile($this->filePath);
                        $total_size = $f->Size;
                    }
                    else
                    {
                        return 0;
                    }
                }
                else
                {
                    $total_size = trim(exec("perl -e 'printf \"%d\n\",(stat(shift))[7];' " . $this->filePath));
                    if (!$total_size || !is_numeric($total_size)) {
                        return 0;
                    }
                }
            }
        }

        // Just to make sure, try and return the filesize() if nothing else
        if ($total_size == 0 || !is_numeric($total_size)) {
            $total_size = filesize($this->filePath);
        }

        return $total_size;
    }

    /**
     * Sets the access permissions of the file
     *
     * @param int $chmod The permission level, as an octal, to set on the file (chmod).
     *
     * @remarks
     *        Permissions:
     *            0 - no permissions,
     *            1 – can execute,
     *            2 – can write,
     *            4 – can read
     *
     *        The octal number is the sum of those three permissions.
     *
     *        Position of the digit in value:
     *            1 - Always zero, to signify an octal value!!
     *            2 - what the owner can do,
     *            3 - users in the file group,
     *            4 - users not in the file group
     *
     * @example
     *        0600 – owner can read and write
     *        0700 – owner can read, write and execute
     *        0666 – all can read and write
     *        0777 – all can read, write and execute
     *
     * @return bool returns the success value of setting the permissions.
     */
    public function setAccess($chmod): bool
    {
        return chmod($this->filePath, $chmod);
    }

    /**
     * Gets the access permissions of the file
     *
     * @return int the permissions on the file
     */
    public function getAccess(): int|false
    {
        return fileperms($this->filePath);
    }

    /**
     * Returns whether this file is writable or not.
     */
    public function isWritable(): bool
    {
        // Attempt to open the file, and read contents
        $handle = @fopen($this->filePath, 'a');
        if ($handle === false) {
            return false;
        }

        // Close the file, return true
        fclose($handle);

        return true;
    }

    /**
     * Returns whether this file is readable or not.
     */
    public function isReadable(): bool
    {
        // Attempt to open the file, and read contents
        $handle = @fopen($this->filePath, 'r');
        if ($handle === false) {
            return false;
        }

        // Close the file, return true
        fclose($handle);

        return true;
    }

    /**
     * Formats a file size to human readable format
     *
     * @param string|float|int The size in bytes
     *
     * @return string Returns a formatted size ( Ex: 32.6 MB )
     */
    protected function formatSize($size): string
    {
        $units = [' B', ' KB', ' MB', ' GB', ' TB'];
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;

        return round($size, 2) . $units[$i];
    }

    /**
     * When used as a string, this object returns the full path to the file.
     */
    public function __toString(): string
    {
        return $this->filePath;
    }
}