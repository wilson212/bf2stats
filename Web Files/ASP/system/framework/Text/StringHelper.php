<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */

namespace System\Text;

class StringHelper
{
    /**
     * Returns a value indicating whether a specified substring occurs within a string.
     *
     * @param string $haystack
     * @param string $needle
     */
    public static function Contains($haystack, $needle): bool
    {
        return (str_contains($haystack, $needle));
    }

    /**
     * Cuts a string to the specified length, while maintaining full words.
     *
     * @param string $text The string we are cutting the length of
     * @param int $maxLength The max length of the string to return
     * @param string $suffix The suffix to add to the end of the string if the string
     *  is too long.
     *
     * @return string
     */
    public static function SubStrWords($text, $maxLength, string $suffix = '...')
    {
        if (strlen($text) > $maxLength)
        {
            // Convert text into an array of words
            $words = preg_split('/\s/', $text);

            // Create buffer, and set length of the suffix
            $output = '';
            $suffixLen = strlen($suffix);
            $i = 0;

            while (true)
            {
                // Calculate length when adding the next word.
                // Add suffix length, as well as +1 for the space
                $length = strlen($output) + strlen($words[$i]) + $suffixLen + 1;
                if ($length > $maxLength) {
                    break;
                }

                $output .= " " . $words[$i];
                ++$i;
            }

            $output .= $suffix;
        }
        else
        {
            $output = $text;
        }

        return $output;
    }

    /**
     * Determines whether the end of a string matches the specified string
     *
     * @param string $string The string to search in (haystack)
     * @param string $value The search text (needle)
     *
     * @return bool true if value matches the end of the string; otherwise false.
     */
    public static function StringEndsWith( $string, $value ): bool
    {
        $len = strlen( $value );
        return substr_compare( $string, $value, -$len, $len ) === 0;
    }

    /**
     * Determines whether the beginning of a string matches the specified string.
     *
     * @param string $string The string to search in (haystack)
     * @param string $value The search text (needle)
     *
     * @return bool true if value matches the beginning of the string; otherwise false.
     */
    public static function StringStartsWith( $string, $value ): bool
    {
        return substr_compare( $string, $value, 0, strlen( $value ) ) === 0;
    }
}