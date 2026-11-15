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
use ArgumentOutOfRangeException;
use ArgumentException;
use OutputSentException;

/**
 * This class is used to send a proper formatted response to the client.
 * You can set headers, cookies, status codes, and protocol within
 * this class.
 *
 * @author      Steven Wilson
 * @package     System
 */
class Response
{
    /**
     * @var string HTTP protocol 1.0
     */
    const HTTP_10 = 'HTTP/1.0';

    /**
     * @var string HTTP protocol 1.1
     */
    const HTTP_11 = 'HTTP/1.1';

    /**
     * @var int The status code to be returned in the response
     */
    protected static $status = 200;

    /**
     * @var string The Response Protocol (HTTP/1.0 | 1.1)
     */
    protected static $protocol = self::HTTP_11;

    /**
     * @var string The Content encoding for this response
     */
    protected static $charset = 'UTF-8';

    /**
     * @var string The Content Mime Type for this response
     */
    protected static $contentType = 'text/html';

    /**
     * @var string[] An array of headers to be sent with the response
     */
    protected static $headers = [];

    /**
     * @var array An array of cookies to be sent with the response
     */
    protected static $cookies = [];

    /**
     * @var string The response body (contents)
     */
    protected static $body;

    /**
     * @var string[] An array of cache directives to be sent with the response
     */
    protected static $cacheDirectives = [];

    /**
     * @var bool Indicates whether the output and headers have been sent already
     */
    protected static $outputSent = false;

    /**
     * @var string[] An array of $statusCode => $description
     */
    protected static $statusCodes = [
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    ];

    /**
     * This method takes all the response headers, cookies, and current
     * buffered contents, and sends them back to the client. After this
     * method is called, any output will most likely cause a content length
     * error for our client.
     *
     *
     * @throws OutputSentException thrown if the headers have been sent already.
     */
    public static function Send(): void
    {
        // Make sure the headers are not sent already
        self::CheckOutputSent();

        // Make sure that if we are redirecting, we set the correct code!
        if (isset(self::$headers['Location']) && self::$status == 200) {
            self::$status = 302;
        }

        // If the status code is 204 or 304, there should be no contents
        if (self::$status == 204 || self::$status == 304) {
            self::$body = '';
        }

        // Send data in order
        self::SendHeader(self::$protocol . " " . self::$status . " " . self::$statusCodes[self::$status]);
        self::SendCookies();
        self::SendContentType();
        self::SendContentLength();
        foreach (self::$headers as $key => $value)
            self::SendHeader($key, $value);

        self::SendBody();

        // Set local var that output has been sent
        self::$outputSent = true;

        // Disable output buffering
        ob_end_flush();
    }

    /**
     * Sets the body of the response
     *
     * @param string $contents
     *
     *
     * @throws OutputSentException thrown if the headers have been sent already.
     */
    public static function SetBody($contents): void
    {
        // Make sure the headers are not sent already
        self::CheckOutputSent();
        self::$body = (string)$contents;
    }

    /**
     * Returns the current body contents of the response
     *
     * @return string
     */
    public static function GetBody()
    {
        return self::$body;
    }

    /**
     * Appends data to the current body
     *
     * @param string $contents The body contents to append.
     */
    public static function AppendBody($contents): void
    {
        // Make sure the headers are not sent already
        self::CheckOutputSent();
        self::$body .= (string)$contents;
    }

    /**
     * Sets or returns the status code.
     *
     * @param int $code The status code to be set
     *
     *
     * @throws ArgumentOutOfRangeException thrown if an invalid status code is passed.
     * @throws OutputSentException thrown if the headers have been sent already.
     */
    public static function SetStatusCode($code): void
    {
        // Are we setting or retrieving?
        $code = (int)$code;
        if (!array_key_exists($code, self::$statusCodes)) {
            throw new ArgumentOutOfRangeException('Invalid HTTP status code: '. $code);
        }

        // Make sure the headers are not sent already
        self::CheckOutputSent();
        self::$status = $code;
    }

    /**
     * Returns the current status code of the response
     *
     * @return int
     */
    public static function GetStatusCode()
    {
        return self::$status;
    }

    /**
     * Sets the content type of the response body
     *
     * @param string $val The content type to be set
     *
     *
     * @throws OutputSentException thrown if the headers have been sent already.
     */
    public static function SetContentType($val): void
    {
        // Make sure the headers are not sent already
        self::CheckOutputSent();

        // Check for included encoding
        if (preg_match('/^(.*);\w*charset\w*=\w*(.*)/i', $val, $matches))
        {
            self::$contentType = $matches[1];
            self::$charset = $matches[2];
        }
        else {
            self::$contentType = (string)$val;
        }
    }

    /**
     * Returns the content type of the response body
     *
     * @return string
     */
    public static function GetContentType()
    {
        return self::$contentType;
    }

    /**
     * Sets the content encoding of the response body
     *
     * @param string $val The content encoding to be set
     *
     *
     * @throws OutputSentException thrown if the headers have been sent already.
     */
    public static function SetEncoding($val): void
    {
        // Make sure the headers are not sent already
        self::CheckOutputSent();
        self::$charset = $val;
    }

    /**
     * Sets a header $key to the given $value
     *
     * @param string $key The header key or name
     * @param string $value The header key's or name's value to be set
     *
     *
     * @throws OutputSentException thrown if the headers have been sent already
     */
    public static function SetHeader($key, $value): void
    {
        // Make sure the headers are not sent already
        self::CheckOutputSent();

        $key = str_replace('_', '-', trim($key));
        if (strtolower($key) === 'content-type')
        {
            self::SetContentType($key);
        }
        else {
            self::$headers[$key] = $value;
        }
    }

    /**
     * Sets a cookies value
     *
     * @param string $name The cookie name
     * @param string $value The cookies value
     * @param int $expires The UNIX timestamp the cookie expires
     * @param string $path The cookie path
     *
     *
     * @throws OutputSentException thrown if the headers have been sent already
     */
    public static function SetCookie($name, $value, $expires, $path = '/'): void
    {
        /// Make sure the headers are not sent already
        self::CheckOutputSent();
        self::$cookies[$name] = [
            'value' => $value,
            'expires' => $expires,
            'path' => $path
        ];
    }

    /**
     * Sets or returns the http protocol
     *
     * @param string $code The protocol to use (HTTP_10 | HTTP_11)
     *
     *
     * @throws ArgumentException thrown if an invalid protocol is passed.
     * @throws OutputSentException thrown if the headers have been sent already
     */
    public static function SetProtocol($code): void
    {
        // Make sure the data was not sent already
        self::CheckOutputSent();

        // Make sure the protocol is valid!
        $code = strtoupper(trim($code));
        if ($code !== self::HTTP_10 || $code !== self::HTTP_11) {
            throw new ArgumentException('Invalid HTTP protocol');
        }

        self::$protocol = $code;
    }

    /**
     * Returns the HTTP protocol that is currently set
     *
     * @return string
     */
    public static function GetProtocol()
    {
        return self::$protocol;
    }

    /**
     * This method sets a redirect header, and status code. When this
     * method is called, if the $wait param is zero, headers
     * will be sent immediately.
     *
     * @param string $location The redirect URL. If a relative path
     *   is passed here, the site's URL will be appended
     * @param int $wait The wait time (in seconds) before the redirect
     *   takes affect. If set to a non 0 value, the page will still be
     *    rendered. Default is 0 seconds.
     * @param int $status The redirect status. 301 is moved permanently,
     *   and 307 is a temporary redirect. Default is 301.
     *
     *
     * @throws OutputSentException thrown if the headers have been sent already
     */
    public static function Redirect($location, $wait = 0, $status = 301): void
    {
        // Make sure the data was not sent already
        self::CheckOutputSent();

        // If we have a relative path, append the site url
        $location = trim($location);
        if (!preg_match('@^((mailto|ftp|http(s)?)://|www\.)@i', $location))
        {
            $location = Request::BaseUrl() . '/' . ltrim($location, '/');
        }

        // Reset all set data, and process the redirect immediately
        if ($wait == 0)
        {
            self::$status = $status;
            self::$headers['Location'] = $location;
            self::$body = '';
            self::Send();
        }
        else
        {
            self::$status = $status;
            self::$headers['Refresh'] = $wait . ';url=' . $location;
        }
    }

    /**
     * Returns a bool of whether a redirect has been set or not
     */
    public static function HasRedirects(): bool
    {
        return (isset(self::$headers['Location']) || isset(self::$headers['Refresh']));
    }

    /**
     * Removes all current redirects that are set
     */
    public static function ClearRedirects(): void
    {
        if (isset(self::$headers['Location'])) {
            unset(self::$headers['Location']);
        }

        if (isset(self::$headers['Refresh'])) {
            unset(self::$headers['Refresh']);
        }
    }

    /**
     * Removes all current headers that are set
     */
    public static function ClearHeaders(): void
    {
        self::$headers = [];
    }

    /**
     * Removes all current cookies that are modified
     */
    public static function ClearCookies(): void
    {
        self::$cookies = [];
    }

    /**
     * Removes all current changes to the response, including the current
     * body buffer
     */
    public static function Reset(): void
    {
        self::$headers = [];
        self::$cookies = [];
        self::$protocol = self::HTTP_11;
        self::$status = 200;
        self::$contentType = 'text/html';
        self::$charset = 'UTF-8';
        self::$body = '';
    }

    /**
     * Returns a bool based on whether the headers and output have been sent
     *
     * @return bool
     */
    public static function OutputSent()
    {
        return self::$outputSent;
    }

    /**
     * Sends all cookies to the client
     *
     * @return void
     */
    protected static function SendCookies()
    {
        foreach (self::$cookies as $key => $values)
        {
            setcookie($key, (string) $values['value'], ['expires' => $values['expires'], 'path' => (string) $values['path'], 'domain' => (string) $_SERVER['HTTP_HOST']]);
        }
    }

    /**
     * Sends a header to the client
     *
     * @param string $name The name of the header
     * @param string $value The value of the header
     */
    protected static function SendHeader($name, $value = null): bool
    {
        // Make sure the headers haven't been sent!
        if (!headers_sent())
        {
            if (is_null($value)) {
                header($name);
            } else {
                header("{$name}: {$value}");
            }

            return true;
        }

        return false;
    }

    /**
     * Sends the contents length to the client
     *
     * @return void
     */
    protected static function SendContentLength()
    {
        // If we already have stuff in the buffer, append that length
        self::$headers['Content-Length'] = $len = ob_get_length() != 0 ? $len + strlen(self::$body) : strlen(self::$body);
    }

    /**
     * Sends the content type to the client
     *
     * @return void
     */
    protected static function SendContentType()
    {
        if (str_starts_with(self::$contentType, 'text/')) {
            self::SetHeader('Content-Type', self::$contentType . "; charset=" . self::$charset);
        } elseif (self::$contentType === 'application/json') {
            self::SetHeader('Content-Type', self::$contentType . "; charset=UTF-8");
        } else {
            self::SetHeader('Content-Type', self::$contentType);
        }
    }

    /**
     * Echo's out the body contents to the client
     *
     * @return void
     */
    protected static function SendBody()
    {
        echo self::$body;
    }

    /**
     * @throws OutputSentException thrown if the output has been sent already
     */
    private static function CheckOutputSent(): void
    {
        if (self::$outputSent) {
            throw new OutputSentException('Response headers have already been sent.');
        }
    }
}