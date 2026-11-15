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
use System\Cache\CacheItem;

/**
 * The AspResponse class is used to properly format
 * the official Gamespy ASP Header and Data output for
 * Awards and player stats.
 *
 * @package System
 *
 * @author Steven Wilson
 */
class AspResponse
{
    /** @var int Standard Gamespy ASP response output. MUST BE USED for BF2 client! */
    const FORMAT_GAMESPY = 0;

    /**
     * @var int Transposed output will print the data
     * to the browser in a different format. All headers will be a new line,
     * with the following data to the left of the header, separated by tabs
     */
    const FORMAT_TRANSPOSED = 1;

    /**
     * @var int JSON formatted response output
     */
    const FORMAT_JSON = 2;

    /**
     * @var int Gets or Sets the output format type
     */
    protected $responseFormat = AspResponse::FORMAT_GAMESPY;

    /**
     * Indicated whether this response starts with an E, or an O
     * @var bool
     */
    protected $error = false;

    /**
     * For bf2142, this contains the error code
     * @var int
     */
    protected $errorCode = 0;

    /**
     * Contains the output lines
     * @var string[]
     */
    protected $lines = [];

    /**
     * @param int $type Sets the response format
     */
    public function setResponseFormat($type): void
    {
        $t = (int)$type;
        switch ($t)
        {
            default:
            case 0:
                $this->responseFormat = AspResponse::FORMAT_GAMESPY;
                break;
            case 1:
                $this->responseFormat = AspResponse::FORMAT_TRANSPOSED;
                break;
            case 2:
                $this->responseFormat = AspResponse::FORMAT_JSON;
                break;
        }
    }

    /**
     * Clears all current output lines
     */
    public function clear(): void
    {
        $this->lines = [];
    }

    /**
     * Takes an infinite amount of parameters. Each param passed
     * is a new header variable, appended to the head tag.
     *
     * Each time this method is called, a new Head tag is created
     */
    public function writeHeaderLine(): void
    {
        $this->lines[] = "H\t" . implode("\t", func_get_args());
    }

    /**
     * Takes an infinite amount of parameters. Each param passed
     * is a new data variable, appended to the data tag.
     *
     * Each time this method is called, a new data line is created
     */
    public function writeDataLine(): void
    {
        $this->lines[] = "D\t" . implode("\t", func_get_args());
    }

    /**
     * This method adds an un-formatted line to the output
     *
     * @param string $line
     */
    public function writeLine($line): void
    {
        $this->lines[] = $line;
    }

    /**
     * This method takes an array of Header => Value, and creates
     * a new Header and Data line for it
     *
     * @param string[] $data
     */
    public function writeHeaderDataArray($data): void
    {
        $this->lines[] = "H\t" . implode("\t", array_keys($data));
        $this->lines[] = "D\t" . implode("\t", array_values($data));
    }

    /**
     * This method takes an array. Each array value is a header variable
     *
     * @param string[] $headers
     */
    public function writeHeaderLineArray($headers): void
    {
        $this->lines[] = "H\t" . implode("\t", $headers);
    }

    /**
     * This method takes an array. Each array value is a data variable
     *
     * @param string[] $data
     */
    public function writeDataLineArray($data): void
    {
        $this->lines[] = "D\t" . implode("\t", $data);
    }

    /**
     * This method is used to define whether this response is to be
     * formatted with the "E" error header
     *
     * @param $bool
     * @param int $code For bf2142 only... specifies the error code
     */
    public function responseError($bool, $code = 0): void
    {
        $this->error = $bool;
        $this->errorCode = $code;
    }

    /**
     * Sends the formatted response to the browser
     *
     * @param CacheItem $item If this response is to be cached, provide the CacheItem
     *  object. otherwise, leave null
     * @param bool $killScript If set to true, the script
     *  will die when this method is called.
     */
    public function send(?CacheItem $item = null, $killScript = true): void
    {
        // Do we cache this response?
        if (is_null($item))
        {
            // output the response
            echo $this->getString();
        }
        else
        {
            $contents = $this->getString();
            $item->set($contents);
            $item->save();
            echo $contents;
        }

        // Kill the script
        if ($killScript) {
            die;
        }
    }

    /**
     * Gets the current response as a string
     *
     * @return string
     *
     * @todo Finish the Json Format
     */
    public function getString()
    {
        // Initial line
        $line = (($this->error) ? "E" : "O");
        if ($this->errorCode != 0) {
            $line .= "\t" . $this->errorCode;
        }

        switch ($this->responseFormat)
        {
            default:
            case AspResponse::FORMAT_GAMESPY:
                // Create a new reference
                $lines = $this->lines;

                // Prepend the response type
                array_unshift($lines, $line);

                // Output the data, adding the number of characters
                $output = implode("\n", $lines);
                $num = strlen((string) preg_replace('/[\t\n]/', '', $output));

                return $output . "\n" . "$\t$num\t$";

            case AspResponse::FORMAT_TRANSPOSED:
                // Add response type
                $lines = [$line];

                // Line counter
                $i = 0;
                foreach ($this->lines as $line)
                {
                    // D's to print
                    $ds = 0;

                    // Only process new lines (headers)
                    if ($line[0] == "H")
                    {
                        // next line
                        $j = $i + 1;

                        // Convert all headers to separate lines
                        $hLines = explode("\t", $line);

                        // Foreach following data line, add to the current header line
                        while (isset($this->lines[$j]) && $this->lines[$j][0] == "D")
                        {
                            // Add each data value to the corresponding header line
                            $data = explode("\t", $this->lines[$j]);
                            $counter = count($hLines);
                            for ($n = 0; $n < $counter; $n++)
                                $hLines[$n] .= "\t" . ($data[$n] ?? "");

                            // Increment line counter, and Increment D's to print
                            $j++;
                            $ds++;
                        }

                        // Add correct number of data columns
                        $hLines[0] = "H" . str_repeat("\tD", $ds);

                        // Add each header line to the lines array
                        $lines = array_merge($lines, $hLines);
                    }

                    $i++;
                }

                // Output the data, adding the number of characters
                $output = implode("\n", $lines);
                $num = strlen((string) preg_replace('/[\t\n]/', '', $output));

                return $output . "\n" . "$\t$num\t$";

            case AspResponse::FORMAT_JSON:
                $response = [
                    'error' => $this->error,
                    'errorCode' => $this->errorCode,
                    'data' => null
                ];
                return json_encode($response, JSON_PRETTY_PRINT);
        }
        return null;
    }
}
