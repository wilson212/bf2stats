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
use ArgumentOutOfRangeException;
use IOException;
use ObjectDisposedException;

/**
 * Provides properties and instance methods for various file stream operations
 *
 * Use the FileStream class to read from, write to, open, and close files on a file system
 *
 * @author      Steven Wilson
 * @package     System
 * @subpackage  IO
 */
class FileStream
{
    // FileMode constant for Read + Write
    const READWRITE = "a+";

    // FileMode constant Read Only
    const READ = "r";

    // FileMode constant Write Only
    const WRITE = "a";

    /**
     * The file stream
     * @var Resource
     */
    protected $stream;

    /**
     * File mode variable
     */
    protected string $mode;

    /**
     * Gets a value indicating whether the current stream supports reading.
     */
    protected bool $canRead;

    /**
     * Gets a value indicating whether the current stream supports writing.
     */
    protected bool $canWrite;

    /**
     * Gets a value indicating whether the current stream is closed.
     * @var  bool
     */
    protected $isClosed;

    /**
     * Specifies which file modes allow reading
     * @var  string[]
     */
    protected static $readModes = ['r', 'r+', 'w+', 'a+', 'x+', 'c+'];

    /**
     * Specifies which file modes allow writing
     * @var  string[]
     */
    protected static $writeModes = ['w', 'a', 'x', 'w+', 'a+', 'x+', 'r+', 'c'];

    /**
     * Constructor
     *
     * @param string $file The full path to the file. If the file does not exist, it will be created
     * @param string $mode The Read / Write mode of the file (See class Constants READ,
     *     WRITE, READWRITE etc ).
     *
     * @throws IOException Thrown if opening of the file stream failed for any reason
     */
    public function __construct($file, $mode = self::READWRITE)
    {
        // Open the file stream
        $this->stream = @fopen($file, $mode);

        // Make sure our stream is valid
        if ($this->stream === false)
        {
            $error = error_get_last();
            if ($error === null) {
                throw new IOException("Unable to open file stream for file \"{$file}\".");
            } else {
                throw new IOException($error["message"]);
            }
        }

        /**
         * Set readable and writable status of the stream
         * Replace both b and t flags, as we don't care about those.
         */
        $this->mode = str_replace(['b', 't'], '', $mode);
        $this->canRead = in_array($mode, self::$readModes);
        $this->canWrite = in_array($mode, self::$writeModes);

        // Set write buffer to 0 to prevent multiple streams on this file messing up
        stream_set_write_buffer($this->stream, 0);
    }

    /**
     * Reads data from file
     *
     * @param int $numBytesToRead The maximum amount of bytes to read.
     *
     * @return string Returns the remaining contents in a string, up to $numBytesToRead bytes
     *                and starting at the specified offset.
     *
     * @throws ObjectDisposedException The stream is closed.
     * @throws IOException The stream does not support reading.
     */
    public function read($numBytesToRead = 1)
    {
        // if a negative number is passed, just read to end
        if ($numBytesToRead < 0) {
            return $this->readToEnd();
        }

        // Ensure the stream is open
        $this->checkDisposed();

        // Ensure we can read from this stream
        $this->ensureCanRead();

        // Read next characters
        return fread($this->stream, $numBytesToRead);
    }

    /**
     * Reads a line of characters from the current stream and returns the data as a string.
     *
     * @param string $delim The end of line delimiter. Do not set unless your having problems
     *     with detecting the end lines, or want to set a custom line break.
     *
     * @return string The next line from the input stream, or null if the end of the input stream is reached.
     *
     * @throws ObjectDisposedException The stream is closed.
     * @throws IOException The stream does not support reading.
     */
    public function readLine($delim = null)
    {
        // Ensure the stream is open
        $this->checkDisposed();

        // Ensure we can read from this stream
        $this->ensureCanRead();

        // If we have no line delimiter, use fgets()
        if ($delim === null) {
            return fgets($this->stream);
        }

        $result = "";
        while (!feof($this->stream))
        {
            // Read next character
            $tmp = fgetc($this->stream);

            // If character is the delim, break
            if ($tmp == $delim) {
                return $result;
            }

            // Append character to string, and continue
            $result .= $tmp;
        }

        return (strlen($result) == 0) ? null : $result;
    }

    /**
     * Reads a CSV formatted line of characters from the current stream and returns the data as an array.
     *
     * @return string[] The next line from the input stream, or null if the end of the input stream is reached.
     *
     * @throws ObjectDisposedException The stream is closed.
     * @throws IOException The stream does not support reading.
     */
    public function readCSVLine()
    {
        // Ensure the stream is open
        $this->checkDisposed();

        // Ensure we can read from this stream
        $this->ensureCanRead();

        return fgetcsv($this->stream);
    }

    /**
     * Reads all characters from the current position to the end of the stream.
     *
     * @return string The rest of the stream as a string, from the current position to the end.
     *        If the current position is at the end of the stream, returns an empty string ("").
     *
     * @throws ObjectDisposedException The stream is closed.
     * @throws IOException The stream does not support reading.
     */
    public function readToEnd(): string
    {
        // Ensure the stream is open
        $this->checkDisposed();

        // Ensure we can read from this stream
        $this->ensureCanRead();

        // Read the stream until the end of file
        $result = "";
        while (!feof($this->stream))
        {
            // Read next character
            $result .= fread($this->stream, 4096);
        }

        return $result;
    }

    /**
     * Reads the next character from the input stream and advances the character position by one character.
     *
     * @return string
     *
     * @throws ObjectDisposedException The stream is closed.
     * @throws IOException The stream does not support reading.
     */
    public function readChar(): string|false
    {
        // Ensure the stream is open
        $this->checkDisposed();

        // Ensure we can read from this stream
        $this->ensureCanRead();

        // Return the next character, or an empty string if we are at the end of stream
        return (feof($this->stream)) ? "" : fgetc($this->stream);
    }

    /**
     * Returns the next available $count of characters, but does not consume them.
     *
     * @param int $count The number of characters to peek from the current stream position
     *
     * @return string The next $count of characters, or null if there are no characters to be read
     *
     * @throws ArgumentOutOfRangeException if $count is negative.
     * @throws ObjectDisposedException The stream is closed.
     * @throws IOException The stream does not support reading.
     */
    public function peek($count = 1): null|string|false
    {
        // Ensure the stream is open
        $this->checkDisposed();

        // We must have a positive number
        $count = (int)$count;
        if ($count < 1) {
            throw new ArgumentOutOfRangeException("Peek count must be greater than zero.");
        }

        // Ensure we can read from this stream
        $this->ensureCanRead();

        // Check if we are at the end of the stream
        if (feof($this->stream)) {
            return null;
        }

        // Read next character
        $result = fread($this->stream, $count);

        // Get previous position. We do not use ftell() here because it does
        // not return the correct position if the file is opened with the WRITE
        // attribute ('a'), and also does not count carriage returns ('\r').
        $position = strlen($result);

        // Reset position in stream
        $this->seek(-$position, SEEK_SET);

        return $result;
    }

    /**
     * Writes to the file stream
     *
     * @param string $stringData The string to write to the file
     *
     * @return int Returns the number of bytes that were written
     *
     * @throws ObjectDisposedException The stream is closed.
     * @throws IOException The stream does not support writing.
     */
    public function write($stringData): int|false
    {
        // Ensure the stream is open
        $this->checkDisposed();

        // Ensure we can write to this stream
        $this->ensureCanWrite();

        return fwrite($this->stream, $stringData);
    }

    /**
     * Writes a line terminator to the text string or stream.
     *
     * @param string $stringData The string to write to the file
     *
     * @return int Returns the number of bytes that were written
     *
     * @throws ObjectDisposedException The stream is closed.
     * @throws IOException The stream does not support writing.
     */
    public function writeLine(string $stringData): int|false
    {
        // Ensure the stream is open
        $this->checkDisposed();

        // Ensure we can write to this stream
        $this->ensureCanWrite();

        return fwrite($this->stream, $stringData . PHP_EOL);
    }

    /**
     * Writes an array to the current file stream, formatted in CSV format.
     *
     *
     * @return int Returns the number of bytes that were written
     */
    public function writeCSVLine(array $dataArray): int|false
    {
        // Ensure the stream is open
        $this->checkDisposed();

        // Ensure we can write to this stream
        $this->ensureCanWrite();

        return fputcsv($this->stream, $dataArray);
    }

    /**
     * Truncates the file to the specified size
     *
     * @param int $size The size to truncate to. If size is larger than the file then the
     *        file is extended with null bytes.
     *
     *
     * @throws ObjectDisposedException The stream is closed.
     * @throws IOException The stream does not support writing.
     */
    public function truncate($size = 0): bool
    {
        // Ensure the stream is open
        $this->checkDisposed();

        // Ensure we can write to this stream
        $this->ensureCanWrite();

        return ftruncate($this->stream, $size);
    }

    /**
     * Alias for FileStream->truncate(0)
     *
     * @param int $size The size to truncate to. If size is larger than the file then the
     *        file is extended with null bytes.
     *
     *
     * @throws ObjectDisposedException The stream is closed.
     * @throws IOException The stream does not support writing.
     */
    public function setLength($size): bool
    {
        // Ensure the stream is open
        $this->checkDisposed();

        // Ensure we can write to this stream
        $this->ensureCanWrite();

        return ftruncate($this->stream, $size);
    }

    /**
     * Gets the length in bytes of the stream.
     *
     * @return int
     *
     * @throws ObjectDisposedException The stream is closed.
     */
    public function getLength()
    {
        // Ensure the stream is open
        $this->checkDisposed();

        // Fetch stream stats using fstat()
        $stat = fstat($this->stream);

        return $stat['size'] ?? 0;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     *
     * @throws ObjectDisposedException The stream is closed.
     */
    public function getPosition(): int
    {
        $this->checkDisposed();

        return (int)ftell($this->stream);
    }

    /**
     * Sets the file position indicator for the file
     *
     * @param int $position The offset, measured in bytes from the beginning of the file
     * @param int $whence The seek constant type (SEEK_SET, SEEK_ CUR, SEEK_END)
     *
     * @see http://www.php.net/manual/en/function.fseek.php
     *
     * @return bool Returns whether the seek was successful
     *
     * @throws ObjectDisposedException The stream is closed.
     */
    public function seek($position, $whence = SEEK_SET): bool
    {
        // Ensure the stream is open
        $this->checkDisposed();

        return (fseek($this->stream, $position, $whence) == 0);
    }

    /**
     * Locks the current file with an advisory level lock
     *
     * @param bool $exclusive
     *
     *
     * @throws ObjectDisposedException The stream is closed.
     */
    public function lock($exclusive = true): bool
    {
        // Ensure the stream is open
        $this->checkDisposed();

        return flock($this->stream, ($exclusive) ? LOCK_EX : LOCK_SH);
    }

    /**
     * Un-Locks the current file
     *
     *
     * @throws ObjectDisposedException The stream is closed.
     */
    public function unlock(): bool
    {
        // Ensure the stream is open
        $this->checkDisposed();

        return flock($this->stream, LOCK_UN);
    }

    /**
     * Flushes the output to a file
     *
     *
     * @throws ObjectDisposedException The stream is closed.
     * @throws IOException The stream does not support writing.
     */
    public function flush(): bool
    {
        // Ensure the stream is open
        $this->checkDisposed();

        // Ensure we can write to the stream
        $this->ensureCanWrite();

        // perform flush
        return fflush($this->stream);
    }

    /**
     * Fetches the file handle stream
     *
     * @return resource

    public function getStream() : resource
     * {
     * return $this->stream;
     * }
     */

    /**
     * Gets a value indicating whether the current stream supports reading.
     *
     * @return bool
     */
    public function canRead()
    {
        return $this->canRead;
    }

    /**
     * Gets a value indicating whether the current stream supports writing.
     *
     * @return bool
     */
    public function canWrite()
    {
        return $this->canWrite;
    }

    /**
     * Closes the file stream
     */
    public function close(): void
    {
        // Don't call close multiple times
        if ($this->isClosed) {
            return;
        }

        // Close the Stream
        fclose($this->stream);
        $this->isClosed = true;
    }

    /**
     * Checks if this stream can be written to, and throws an exception if not.
     *
     * @return void
     *
     * @throws ObjectDisposedException The stream is closed.
     */
    protected function checkDisposed()
    {
        // Ensure we can write to this stream
        if ($this->isClosed || !is_resource($this->stream)) {
            throw new ObjectDisposedException("The stream is closed.");
        }
    }

    /**
     * Checks if this stream can be written to, and throws an exception if not.
     *
     * @return void
     *
     * @throws IOException The stream does not support writing.
     */
    protected function ensureCanWrite()
    {
        // Ensure we can write to this stream
        if (!$this->canWrite) {
            throw new IOException("The stream does not support writing.");
        }
    }

    /**
     * Checks if this stream can supports reading, and throws an exception if not.
     *
     * @return void
     *
     * @throws IOException The stream does not support reading.
     */
    protected function ensureCanRead()
    {
        // Ensure we can read from this stream
        if (!$this->canRead) {
            throw new IOException("The stream does not support reading.");
        }
    }
}