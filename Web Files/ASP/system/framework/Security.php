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
use System\Net\IPAddress;

/**
 * Security Class
 *
 * @author      Steven Wilson
 * @package     Asp
 */
class Security
{
    /**
     * @var int Specifies the lifetime of the cookie in seconds which is sent to the browser.
     * @see http://php.net/manual/en/session.configuration.php#ini.session.cookie-lifetime
     */
    const SESSION_COOKIE_LIFETIME = 0;

    /**
     * Takes a username and password, and returns attempts to log the
     * user in.
     *
     * @param string $username The username logging in
     * @param string $password The unencrypted password
     */
    public static function Login($username, $password): bool
    {
        // Check if the username and password matches the config
        $configuredPassword = (string)Config::Get('admin_pass');
        if (is_string($username) && is_string($password)
            && $configuredPassword !== '' && $configuredPassword !== 'admin'
            && hash_equals((string)Config::Get('admin_user'), $username)
            && hash_equals($configuredPassword, $password))
        {
            session_regenerate_id(true);
            unset($_SESSION['csrfToken']);
            // Start Session, set session variables
            $_SESSION['adminAuth'] = sha1(Config::Get('admin_user') . ':' . Config::Get('admin_pass'));
            $_SESSION['adminTime'] = time();

            // Update last login time
            $last = Config::Get('admin_current_login');
            Config::Set('admin_last_login', $last);
            Config::Set('admin_current_login', $_SESSION['adminTime']);
            Config::Save();

            return true;
        }

        return false;
    }

    /**
     * Logs the user out and sets all session variables to Guest.
     */
    public static function Logout(): void
    {
        // If sessions is already killed, just return
        if (!self::IsValidSession()) {
            return;
        }

        // Retain a fresh anonymous session so the next login form's CSRF token
        // can be persisted immediately after logout.
        $_SESSION = [];
        session_regenerate_id(true);
    }

    public static function IsValidSession(): bool
    {
        // Session isn't set
        if (!isset($_SESSION['adminAuth'])) {
            return false;
        }

        // If the password set is wrong
        if (!is_string($_SESSION['adminAuth']) || !hash_equals(
            sha1(Config::Get('admin_user') . ':' . Config::Get('admin_pass')), $_SESSION['adminAuth'])) {
            return false;
        }

        // If the session time is expired
        if (!isset($_SESSION['adminTime']) || $_SESSION['adminTime'] < time() - (30 * 60)) {
            return false;
        }

        // Everything is good, update the session time
        $_SESSION['adminTime'] = time();

        return true;
    }

    /**
     * @param $ip
     *
     * @return bool
     */
    public static function IsAuthorizedIp($ip)
    {
        $hosts = Config::Get('admin_hosts');
        return IPAddress::IsInCIDR($ip, $hosts);
    }

    public static function CsrfToken(): string
    {
        if (empty($_SESSION['csrfToken'])) {
            $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrfToken'];
    }

    public static function IsValidCsrfToken($token): bool
    {
        return is_string($token) && isset($_SESSION['csrfToken'])
            && hash_equals($_SESSION['csrfToken'], $token);
    }
}

session_start([
    'use_strict_mode' => true,
    'use_only_cookies' => true,
    'cookie_lifetime' => Security::SESSION_COOKIE_LIFETIME,
    'cookie_httponly' => true,
    'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'cookie_samesite' => 'Strict'
]);
