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
 * Class StringReader
 * @package System\IO
 */
class StringReader
{
    /**
     * @var int Internal string position
     */
    private $position;

    /**
     * @var int The length of the internal string
     */
    private readonly int $length;

    /**
     * @var string The internal string
     */
    private $contents;

    /**
     * StringReader constructor.
     *
     * @param $contents
     * @param bool $removeCarriageReturns
     */
    public function __construct($contents, $removeCarriageReturns = false)
    {
        $this->contents = ($removeCarriageReturns) ? str_replace("\r\n", "\n", $contents) : $contents;
        $this->length = strlen((string) $contents) - 1;
    }

    /**
     * Returns whether the current position of the string position is at the end.
     */
    public function eof(): bool
    {
        return $this->position >= $this->length;
    }

    /**
     * Resets the internal string pointer back to the start of the string
     */
    public function reset(): void
    {
        $this->position = 0;
    }

    /**
     * Reads the next character from the string and advances the character
     * position by one character.
     *
     * @return string
     */
    public function readChar()
    {
        if ($this->eof()) {
            return "";
        }

        $c = $this->peek();
        $this->position += 1;

        return $c;
    }

    /**
     * Reads a line of characters from the string and returns the data as a string.
     */
    public function readLine(): string
    {
        // Our return variable
        $return = '';

        // Read until the end of string, or we hit a newline
        while (!$this->eof())
        {
            if ($this->peek() == "\n")
            {
                $this->readChar();
                break;
            }

            $return .= $this->readChar();
        }

        return $return;
    }

    /**
     * Reads all characters from the current position to the end of the string
     * and returns them as one string.
     */
    public function readToEnd(): string
    {
        if ($this->eof()) {
            return '';
        }

        $return = substr($this->contents, $this->position);
        $this->position = $this->length;
        return $return;
    }

    /**
     * Reads the internal string from the current position until the search text is found.
     *
     * @param string $search the text to stop consuming at.
     * @param string $contents
     * @param bool $consume if true the search text will be consumed, but
     *  not returned, otherwise the search text is not consumed.
     *
     * @return bool
     */
    public function readUntil($search, &$contents, $consume = false)
    {
        $contents = '';
        $found = false;
        $len = strlen($search);

        if ($this->position + $len >= $this->length) {
            $contents = $this->readToEnd();
        } elseif ($len == 1) {
            while (!$this->eof())
            {
                if ($this->peek() == $search)
                {
                    $found = true;
                    break;
                }

                $contents .= $this->readChar();
            }
        } else
        {
            $end = strpos($this->contents, $search, $this->position);
            if ($end !== false)
            {
                $length = ($end - $this->position);
                $contents = substr($this->contents, $this->position, $length);

                // Update string position
                $this->position += $length;
                $found = true;
            }
            else {
                $contents = $this->readToEnd();
            }
        }

        // consume?
        if ($consume) {
            $this->position += $len;
        }

        return $found;
    }

    /**
     * Gets the internal string position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the internal string position to the specified index
     *
     * @param int $index
     */
    public function setPosition($index): void
    {
        $this->position = (int)$index;
    }

    public function getFilePosition(&$line, &$column): void
    {
        // Get line number
        $line = substr_count($this->contents, "\n", 0, $this->position) + 1;

        // Get column we are at from the last new line
        $contents = substr($this->contents, 0, $this->position);
        $pos = strrpos($contents, "\n");
        $column = ($this->position - $pos) + 1;
    }

    /**
     * Returns the next character in the SQL string, but does
     * not consume it.
     *
     * @param int $n offset from the current position
     *
     * @return string
     */
    public function peek($n = 0)
    {
        $newIndex = $this->position + $n;
        if ($newIndex >= $this->length) {
            return "";
        }

        return $this->contents[$newIndex];
    }

    /**
     * Takes the next character in the string if it is whitespace
     */
    public function takeWhiteSpace(): void
    {
        // Skip next whitespace characters from query
        while (!$this->eof() && $this->nextIsWhiteSpace())
            $this->readChar();
    }

    /**
     * Determines whether the next character is whitespace
     */
    public function nextIsWhiteSpace(): bool
    {
        return trim($this->peek()) === '';
    }
}