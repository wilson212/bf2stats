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
use IOException;
use System\IO\FileStream;

/**
 * LogWritter Class, inspired by KLogger
 *
 * @author      Steven Wilson
 * @package     System
 */
class LogWriter
{
    /**
     * Security level constant
     * @var int
     */
    const SECURITY = 0;

    /**
     * Error level constant
     * @var int
     */
    const ERROR = 1;

    /**
     * Warning level constant
     * @var int
     */
    const WARN = 2;

    /**
     * Notice level constant
     * @var int
     */
    const NOTICE = 3;

    /**
     * Debug level constant
     * @var int
     */
    const DEBUG = 4;

    /**
     * An array of logger instances for different log files.
     * @var LogWriter[]
     */
    protected static $logs = [];

    /**
     * The log files resource stream
     * @var resource
     */
    protected FileStream $file;

    /**
     * The level in which the logger should log
     * @var int
     */
    protected $logLevel = self::DEBUG;

    /**
     * The log date format
     * @var string
     */
    protected $dataFormat = "Y-m-d H:i:s";

    /**
     * An array of logs to write to the file
     * @var string[]
     */
    protected $messages = [];

    /**
     * Constructor
     *
     * @param string $filepath File path to the log file. If left null, a temporary file
     *   will be created using php's {@link tmpfile()} function, and removed once the
     *   current php script finishes.
     * @param string $instanceName The instance id (used for retrieving this instance later)
     *
     * @throws IOException Thrown if opening of the file stream failed for any reason
     */
    public function __construct($filepath = null, $instanceName = null)
    {
        // Create our FileStream instance
        $this->file = new FileStream($filepath, FileStream::WRITE);

        // Add instance
        if (!empty($instanceName)) {
            self::$logs[$instanceName] = $this;
        }
    }

    /**
     * Sets the minimum log level in order to log messages
     *
     * @param int $level The minimum log level to record the message
     */
    public function setLogLevel($level): void
    {
        $this->logLevel = $level;
    }

    /**
     * Writes a line to the log without pre-pending a status or timestamp
     *
     * @param string $string The line to write to the log file
     */
    public function writeLine($string): void
    {
        $this->messages[] = $string;
    }

    /**
     * Writes a line to the log with the severity of SECURITY
     *
     * @param string $message The line to write to the log file
     * @param bool|\string[] $args An array of replacements in the string
     */
    public function logSecurity($message, $args = false): void
    {
        // Make sure we want to log this
        if ($this->logLevel >= self::SECURITY) {
            $this->writeLine($this->format($message, $args, self::SECURITY));
        }
    }

    /**
     * Writes a line to the log with the severity of ERROR
     *
     * @param string $message The line to write to the log file
     * @param bool|\string[] $args An array of replacements in the string
     */
    public function logError($message, $args = false): void
    {
        // Make sure we want to log this
        if ($this->logLevel >= self::ERROR) {
            $this->writeLine($this->format($message, $args, self::ERROR));
        }
    }

    /**
     * Writes a line to the log with the severity of DEBUG
     *
     * @param string $message The line to write to the log file
     * @param bool|\string[] $args An array of replacements in the string
     */
    public function logDebug($message, $args = false): void
    {
        // Make sure we want to log this
        if ($this->logLevel >= self::DEBUG) {
            $this->writeLine($this->format($message, $args, self::DEBUG));
        }
    }

    /**
     * Writes a line to the log with the severity of WARN
     *
     * @param string $message The line to write to the log file
     * @param bool|\string[] $args An array of replacements in the string
     */
    public function logWarning($message, $args = false): void
    {
        // Make sure we want to log this
        if ($this->logLevel >= self::WARN) {
            $this->writeLine($this->format($message, $args, self::WARN));
        }
    }

    /**
     * Writes a line to the log with the severity of NOTICE
     *
     * @param string $message he line to write to the log file
     * @param bool|\string[] $args An array of replacements in the string
     */
    public function logNotice($message, $args = false): void
    {
        // Make sure we want to log this
        if ($this->logLevel >= self::NOTICE) {
            $this->writeLine($this->format($message, $args, self::NOTICE));
        }
    }

    /**
     * Acts as a singleton to fetch a logger object with the given ID
     *
     * @param string|int $id The instance id or name that was provided in the logger class'
     *   constructor.
     *
     * @return LogWriter|bool Returns false if the $id was never set.
     */
    public static function Instance($id)
    {
        return self::$logs[$id] ?? false;
    }

    /**
     * Formats the message with a timestamp, log level, and replace's
     * the sprints with the supplied arguments
     *
     * @param string $message The message to be formatted and logged
     * @param string|string[] $args An array of replacements for the $message sprints
     * @param bool|int $mode The log level of this message.
     *
     * @return string Returns the formatted message.
     */
    public function format($message, $args, $mode = false): string
    {
        // Trim message
        $message = trim($message);

        // Correctly format args
        if (!empty($args))
        {
            if (!is_array($args)) {
                $args = [$args];
            }

            $message = vsprintf($message, $args);
        }

        // Process initial string value
        $start = date($this->dataFormat, time());
        return match ($mode) {
            self::SECURITY => $start . ' -- SECURITY: ' . $message,
            self::ERROR => $start . ' -- ERROR: ' . $message,
            self::DEBUG => $start . ' -- DEBUG: ' . $message,
            self::WARN => $start . ' -- WARNING: ' . $message,
            self::NOTICE => $start . ' -- NOTICE: ' . $message,
            default => $start . ' -- INFO: ' . $message,
        };
    }

    /**
     * Sets the date format used inside the log file
     *
     * @param string $format Valid format string for date()
     */
    public function setDateFormat($format): void
    {
        $this->dataFormat = $format;
    }

    public function flush(): void
    {
        // Empty message Queue
        if ($this->file instanceof FileStream && !empty($this->messages)) {
            $this->file->write(implode(PHP_EOL, $this->messages) . PHP_EOL);
            $this->messages = [];
        }
    }

    /**
     * Class Destructor. Closes the file handle.
     *
     * @return void
     */
    public function __destruct()
    {
        // Close file if its open
        if ($this->file instanceof FileStream)
        {
            // Empty message Queue
            if (!empty($this->messages)) {
                $this->file->write(implode(PHP_EOL, $this->messages) . PHP_EOL);
            }

            $this->file->close();
        }
    }
}