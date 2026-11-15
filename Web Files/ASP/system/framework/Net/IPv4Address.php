<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
namespace System\Net;

use Stringable;
use InvalidArgumentException;

/**
 * Provides an Internet Protocol (IP) address.
 *
 * @package System\Net
 */
class IPv4Address implements iIPAddress, Stringable
{
    /**
     * @var string The ip address string
     */
    protected string $ipAddress;

    /**
     * @var bool Indicates whether this is a local IP address
     */
    protected bool $isLocal;

    /**
     * IPv4Address constructor.
     *
     * @param string $address An IPv4 address.
     */
    public function __construct($address)
    {
        // Check for CIDR ranges
        $parts = explode('/', $address);

        // Make sure IP is valid!
        if (!filter_var($parts[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new InvalidArgumentException("IPv4 Address is invalid!");
        }

        // Define local properties
        $flags = FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
        $this->ipAddress = $parts[0];
        $this->isLocal = (str_starts_with($parts[0], "127.") || !filter_var($parts[0], FILTER_VALIDATE_IP, $flags));
    }

    /**
     * Returns whether this IP address is the loopback address (Localhost)
     *
     * @return bool
     */
    public function isLoopback()
    {
        return $this->isLocal;
    }

    /**
     * Indicates whether this address falls under the supplied CIDR
     *
     * @param string|iIPAddress $address the CIDR address range to compare against this IPAddress
     *  instance.
     *
     * @return bool true if this IPAddress fulls under the supplied CIDR range. If no range is supplied,
     *  this address will be directly compared and will return whether both addresses are equal.
     *
     * @see https://www.ipaddressguide.com/cidr
     */
    public function isInCidr($address)
    {
        if ($address instanceof IPv4Address)
        {
            $address = $address->toString();
        }

        // if no forward slash, just compare
        if (!str_contains($address, '/'))
        {
            return $this->equals($address);
        }

        [$subnet, $bits] = explode('/', $address);
        $ip = ip2long($this->ipAddress);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask; // in case the supplied subnet was not correctly aligned
        return ($ip & $mask) === $subnet;
    }

    /**
     * Returns whether this IP Address is equal to the supplied IP
     *
     * @param string|iIPAddress $Ip The IPAddress to compare to
     */
    public function equals($Ip): bool
    {
        if ($Ip instanceof IPv4Address) {
            return ($Ip->toString() == $this->ipAddress);
        } else {
            return ($this->ipAddress == $Ip);
        }
    }

    /**
     * Returns the IP address type (@see IPAddress::IP_VERSION_*)
     */
    public function getType(): int
    {
        return IPAddress::IP_VERSION_4;
    }

    /**
     * Returns the IPv4 dotted-quad notation.
     *
     * @return string
     */
    public function toString()
    {
        return $this->ipAddress;
    }

    /**
     * Returns the string representation of this IPAddress
     */
    public function __toString(): string
    {
        return $this->ipAddress;
    }

    /**
     * Maps the IPAddress object to an IPv6 address.
     *
     * @return IPv6Address
     */
    public function mapToIPv6()
    {
        return IPAddress::IPv4To6($this);
    }

    /**
     * Maps the IPAddress object to an IPv4 address.
     */
    public function mapToIPv4(): static
    {
        return $this;
    }
}