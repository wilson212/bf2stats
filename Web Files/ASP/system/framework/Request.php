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

/**
 * This class provides information for the current Request, such as
 * all the Post and GET data, the URI string, the Remote IP, Referrer,
 * the base URL, website root, and more.
 *
 * @author      Steven Wilson
 * @package     System
 */
class Request
{
    /**
     * @var string HTTP protocol method
     */
    const PROTOCOL_HTTP = 'http';

    /**
     * @var string HTTPS protocol method
     */
    const PROTOCOL_HTTPS = 'https';

    /**
     * @var string FTP protocol method
     */
    const PROTOCOL_FTP = 'ftp';

    /**
     * @var string SSL protocol method
     */
    const PROTOCOL_SSL = 'ssl';

    /**
     * @var string POST method
     */
    const METHOD_POST = 'POST';

    /**
     * @var string GET method
     */
    const METHOD_GET = 'GET';

    /**
     * @var string PUT method
     */
    const METHOD_PUT = 'PUT';

    /**
     * @var string DELETE method
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * @var string Current protocol
     */
    protected static $protocol = 'http';

    /**
     * @var string The site's base url (the root of the website)
     */
    protected static $baseurl;

    /**
     * @var string Http domain name (no trailing paths after the .com)
     */
    protected static $domain;

    /**
     *
     * @var string The trailing path after the domain name.
     */
    protected static $webroot;

    /**
     * @var string The query string passed with the request
     */
    protected static $queryString;

    /**
     * @var string The remote IP address connected to this request
     */
    protected static $clientIp;

    /**
     * Class Constructor (called automatically)
     *
     * Initializes the class properties
     */
    public static function Init(): void
    {
        if (empty(self::$domain))
        {
            // Define our domain and webroot
            self::$domain = rtrim((string) $_SERVER['HTTP_HOST'], '/');
            self::$webroot = dirname((string) $_SERVER['PHP_SELF']);

            // Detect our protocol
            if (isset($_SERVER['HTTPS']))
            {
                $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
                self::$protocol = ($secure) ? 'https' : 'http';
            }
            else {
                self::$protocol = 'http';
            }

            // build our base url
            $site_url = preg_replace('~(/{2,})~', '/', self::$domain . '/' . self::$webroot);
            self::$baseurl = str_replace('\\', '', self::$protocol . '://' . rtrim((string) $site_url, '/'));
        }
    }

    /**
     * Returns data from the 'php://input'
     *
     * @return string
     */
    public static function Input(): string|false
    {
        return file_get_contents('php://input');
    }

    /**
     * Returns the request http method (GET, POST, PUT etc)
     *
     * @return string
     */
    public static function Method()
    {
        $val = null;
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $val = strtoupper((string) $_SERVER['REQUEST_METHOD']);
        } elseif (getenv('REQUEST_METHOD') !== false) {
            $val = strtoupper(getenv('REQUEST_METHOD'));
        }

        return $val;
    }

    /**
     * Returns the referring website url
     *
     * @return string
     */
    public static function Referrer()
    {
        $ref = null;
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $ref = $_SERVER['HTTP_X_FORWARDED_HOST'];
        } elseif (isset($_SERVER['HTTP_REFERRER'])) {
            $ref = $_SERVER['HTTP_REFERRER'];
        }

        return $ref;
    }

    /**
     * Returns the url query string
     *
     * @param string $key The GET array id to return. Leave null to return all GET data
     * @param mixed $default The default return value if the GET array key doesn't
     *    exist. Default is null.
     *
     * @return string|string[]|mixed Returns $default if the GET key doesn't exist. Returns a
     *   string[] if no $key is provided, or the value of $key if the array key exists
     */
    public static function Query($key = null, $default = null)
    {
        if ($key == null) {
            return $_SERVER['QUERY_STRING'];
        }

        return (array_key_exists($key, $_GET)) ? $_GET[$key] : $default;
    }

    /**
     * Returns the POST var specified, or all POST data
     *
     * @param string $key The POST array id to return. Leave null to return all POST data
     * @param mixed $default The default return value if the POST array key doesn't
     *    exist. Default is null.
     *
     * @return string|string[]|mixed Returns $default if the POST key doesn't exist. Returns a
     *   string[] if no $key is provided, or the value of $key if the array key exists.
     */
    public static function Post($key = null, $default = null)
    {
        if ($key == null) {
            return $_POST;
        }

        return (array_key_exists($key, $_POST)) ? $_POST[$key] : $default;
    }

    /**
     * Returns the Cookie name specified, or all Cookie data
     *
     * @param string $key The cookie name to return. Leave null to return all cookie data
     * @param mixed $default The default return value if the Cookie name doesn't
     *    exist. Default is null.
     *
     * @return string|string[]|mixed Returns $default if the Cookie name doesn't exist. Returns a
     *   string[] if no $key is provided, or the value of $key if the cookie exists.
     */
    public static function Cookie($key = null, $default = null)
    {
        if ($key == null) {
            return $_COOKIE;
        }

        return (array_key_exists($key, $_COOKIE)) ? $_COOKIE[$key] : $default;
    }

    /**
     * Returns the an array of what formats the client is accepting
     *
     * @param string $type The type to return
     *
     * @todo Finish the method, and provide better description
     * @return void
     */
    public static function Accepts($type = null)
    {

    }

    /**
     * Returns a string or string[] of what languages the client accepts
     *
     * @param string $lang If a language is provided here, the method will return
     *    true or false based on whehter the client accepts the language
     *
     * @return string|string[]|bool Returns the language, or an array of
     * languages the client accepts. If $lang is set, then this method returns
     * a bool based on whether the client accepts the language
     */
    public static function AcceptsLanguage($lang = null)
    {
        $accepts = preg_split('/[;,]/', (string) self::Header('Accept-Language'));
        foreach ($accepts as &$accept)
        {
            $accept = strtolower($accept);
            if (str_contains($accept, '_')) {
                $accept = str_replace('_', '-', $accept);
            }
        }

        return ($lang === null) ? $accepts : in_array($lang, $accepts);
    }

    /**
     * Returns the Remote connected IP address
     *
     * @return string The validated remote IP address. Returns 0.0.0.0 if
     *   the IP address could not be determined
     */
    public static function ClientIp()
    {
        // Return it if we already determined the IP
        if (empty(self::$clientIp))
        {
            // Check to see if the server has the IP address
            if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
            {
                self::$clientIp = $_SERVER['HTTP_CLIENT_IP'];
            }
            elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']))
            {
                self::$clientIp = $_SERVER['REMOTE_ADDR'];
            }

            // If we still have a false IP address, then set to 0's
            if (empty(self::$clientIp)) {
                self::$clientIp = '0.0.0.0';
            }
        }

        return self::$clientIp;
    }

    /**
     * @return bool
     */
    public static function IsLocalHost()
    {
        $clientIp = Request::ClientIp();
        return in_array($clientIp, array('127.0.0.1', '::1')) || $_SERVER['SERVER_ADDR'] == $clientIp;
    }

    /**
     * Returns the whether the request is an ajax request
     *
     * @return bool If the request is an ajax request
     */
    public static function IsAjax()
    {
        if (isset($_POST['ajax'])) {
            return filter_var($_POST['ajax'], FILTER_VALIDATE_BOOLEAN);
        } else {
            return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        }
    }

    /**
     * Indicates whether the request method is a GET request
     */
    public static function IsGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == "GET";
    }

    /**
     * Indicates whether the request method is a POST request
     */
    public static function IsPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == "POST";
    }

    /**
     * Returns the value of the specified header passed
     *
     * @param string $name The header name to be returned
     *
     * @return string|null Returns false if the header isn't set
     */
    public static function Header($name)
    {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return (empty($_SERVER[$name])) ? null : $_SERVER[$name];
    }

    /**
     * Returns the site's base URL
     *
     * @return string
     */
    public static function BaseUrl()
    {
        return self::$baseurl;
    }

    /**
     * Returns the current requests protocol
     *
     * @return string
     */
    public static function Protocol()
    {
        return self::$protocol;
    }
}

// Init the class
Request::Init();