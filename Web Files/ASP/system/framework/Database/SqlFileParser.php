<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
namespace System\Database;
use System\IO\File;

/**
 * A simple class that parses SQL files into an array of statements.
 *
 * This parser supports all MySQL statements including delimiter changes.
 * Any and all SQL comments are stripped before parsing begins.
 *
 * @author Tony Hudgins
 * @author Steven Wilson
 *
 * @package System\Database
 */
class SqlFileParser
{
    /**
     * @var int Internal SQL string position
     */
    private ?int $position = null;

    /**
     * @var int The length of the internal $sql string
     */
    private int $length;

    /**
     * @var string The internal SQL string
     */
    private $sql;

    /**
     * SqlFileParser constructor.
     *
     * @param string $filePath The file path to the SQL file
     */
    public function __construct($filePath)
    {
        // Get SQl statements
        $contents = File::ReadAllText($filePath);

        // Prepare query for comment extraction!
        $this->length = strlen($contents);
        $this->sql = $contents;

        // Remove comments
        $this->sql = $this->removeComments();
        $this->length = strlen($this->sql);
    }

    /**
     * Fetches all SQL statements as an array. Each array element is a single SQL statement.
     *
     * @remarks All comments are excluded from the array.
     *
     * @return string[]
     */
    public function getStatements(): array
    {
        $sqlList = [];

        $query = "";
        $delimiter = ';';
        $this->position = 0;

        // Parsing the SQL file content
        while (!$this->eof())
        {
            // Do not parse quoted strings
            if ($this->peek() === "'" || $this->peek() === "\"")
            {
                $term = $this->peek();
                $query .= $this->take();

                // Skip until we hit our quote term
                while ($this->peek() !== $term && !$this->eof())
                {
                    // Quote term escaped? If so take twice and keep skipping
                    if ($this->peek() === "\\" && $this->peek(1) === $term) {
                        $query .= $this->take();
                    }

                    $query .= $this->take();
                }

                // Append character to query builder
                $query .= $this->take();
                continue;
            }

            // Check for delimiter change
            if ($this->takeIfNext('delimiter', true))
            {
                // Take next whitespace
                $this->takeWhiteSpace();

                $delimiter = '';
                while (!$this->eof() && !$this->nextIsWhiteSpace())
                    $delimiter .= $this->take();

                // Take whitespace to next statement
                $this->takeWhiteSpace();
                continue;
            }

            // Check for end of statement
            if ($this->takeIfNext($delimiter))
            {
                // Store this query
                $sqlList[] = trim($query);

                // Reset the variable
                $query = "";

                // Remove whitespace from the start of next query
                $this->takeWhiteSpace();
                continue;
            }

            // Append character to query builder
            $query .= $this->take();
        }

        if (strlen($query) > 0) {
            $sqlList[] = trim($query);
        }

        return $sqlList;
    }

    /**
     * Returns any and all comments from the SQL string, and returns the result
     */
    protected function removeComments(): string
    {
        $this->position = 0;
        $clean = "";

        while (!$this->eof())
        {
			// Take all quoted items
            if ($this->peek() === "'" || $this->peek() === "\"")
            {
                $term = $this->peek();
                $clean .= $this->take();

                while ($this->peek() !== $term && !$this->eof())
                {
                    if ($this->peek() === "\\" && $this->peek(1) === $term) {
                        $clean .= $this->take();
                    }

                    $clean .= $this->take();
                }

                $clean .= $this->take();
                continue;
            }

			// Remove single line comments
            if ($this->takeIfNext("--") || $this->takeIfNext("#"))
            {
				// Take until we hit a new line
                while ($this->peek() !== "\n" && !$this->eof())
                    $this->take();

                $clean .= $this->take();
                continue;
            }

			// Remove Multi-line comments
            if ($this->takeIfNext("/*"))
            {
                $nest = 1;
                while ($nest > 0 && !$this->eof())
                {
                    if ($this->takeIfNext("*/"))
                    {
                        --$nest;
                        continue;
                    }

                    if ($this->takeIfNext("/*"))
                    {
                        ++$nest;
                        continue;
                    }

                    $this->take();
                }
            }

            $clean .= $this->take();
        }

		// Remove last delimiter
        //if (strrpos($clean, ";") === strlen($clean) - 1)
            //$clean = substr($clean, 0, strlen($clean) - 1);

        return $clean;
    }

    /**
     * Returns whether the current position of SQL string position is at the end.
     */
    private function eof(): bool
    {
        return $this->position >= $this->length;
    }

    /**
     * Determines if the next set of characters matches the provided string.
     *
     * @param string $search the character(s) to check against the current SQL string position
     * @param bool $caseSensitive true if case sensitive search, false otherwise
     *
     * @return bool
     */
    private function isNext(string $search, bool $caseSensitive = true)
    {
        $len = strlen($search);
        if ($this->position + $len >= $this->length) {
            return false;
        }

        $compare = substr($this->sql, $this->position, $len);
        return ($caseSensitive)
            ? $search === $compare
            : strtolower($search) === strtolower($compare);
    }

    /**
     * Determines if the next set of characters matches the provided string,
     * and consumes them if true.
     *
     * @param string $search the character(s) to consume if next in the SQL string
     * @param bool $caseSensitive true if case sensitive search, false otherwise
     *
     * @return bool true if the $search matched the next set of character(2), otherwise false
     */
    private function takeIfNext(string $search, bool $caseSensitive = true): bool
    {
        if ($this->isNext($search, $caseSensitive))
        {
            $this->position += strlen($search);
            return true;
        }

        return false;
    }

    /**
     * Returns the next character in the SQL string, but does
     * not consume it.
     *
     * @param int $n offset from the current position
     *
     * @return string
     */
    private function peek(int $n = 0)
    {
        $newIndex = $this->position + $n;
        if ($newIndex >= $this->length) {
            return "";
        }

        return $this->sql[$newIndex];
    }

    /**
     * Takes the next character in the SQL string
     *
     * @return string
     */
    private function take()
    {
        if ($this->eof()) {
            return "";
        }

        $c = $this->peek();
        $this->position += 1;

        return $c;
    }

    /**
     * Takes the next character in the string if it is whitespace
     */
    public function takeWhiteSpace(): void
    {
        // Skip next whitespace characters from query
        while (!$this->eof() && $this->nextIsWhiteSpace())
            $this->take();
    }

    /**
     * Determines whether the next character is whitespace
     */
    public function nextIsWhiteSpace(): bool
    {
        return trim($this->peek()) === '';
    }
}