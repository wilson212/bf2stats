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
use Exception;

/**
 * Provides an Internet Protocol (IP) address.
 *
 * @package System\Net
 */
class IPv6Address implements iIPAddress, Stringable
{
    /**
     * The original IP address that was passed
     */
    protected string $ipAddress;

    /**
     * Full expanded IP address
     * @var string
     */
    protected $fullIpAddress = "";

    /**
     * Indicates whether the IP address is local
     */
    protected bool $isLocal;

    /**
     * IPv6Address constructor.
     *
     * @param string $address An IPv6 address.
     */
    public function __construct($address)
    {
        // Check for CIDR ranges
        $parts = explode('/', $address);

        // Make sure IP is valid!
        if (!filter_var($parts[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            throw new InvalidArgumentException("IPv6 Address is invalid!");
        }

        $flags = FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
        $this->ipAddress = $parts[0];
        $this->fullIpAddress = IPAddress::ExpandIPv6Notation($parts[0], true);
        $this->isLocal = !filter_var($parts[0], FILTER_VALIDATE_IP, $flags);
    }

    /**
     * Indicates whether the specified IP address is the loopback address.
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
     * @see https://www.ipaddressguide.com/ipv6-cidr
     */
    public function isInCidr($address)
    {
        if ($address instanceof IPv6Address)
        {
            $address = $address->toString();
        }

        // if no forward slash, just compare
        if (!str_contains($address, '/'))
        {
            return $this->equals($address);
        }

        [$subnet, $mask] = explode('/', $address);
        $subnet = inet_pton($subnet);
        $addr = inet_pton($this->ipAddress);
        $binMask = $this->iPv6MaskToByteArray($mask);
        return ($addr & $binMask) == $subnet;
    }

    /**
     * Compares the current IPAddress instance with the comparing parameter and returns true
     *  if the two instances contain the same IP address.
     *
     * @param string|iIPAddress $Ip The IP address to compare with
     *
     * @return bool
     */
    public function equals($Ip)
    {
        if ($Ip instanceof IPv6Address) {
            return $Ip->equals($this->fullIpAddress);
        } else {
            return ($this->fullIpAddress == IPAddress::ExpandIPv6Notation($Ip));
        }
    }

    /**
     * Returns the IP address type (@see IPAddress::IP_VERSION_*)
     */
    public function getType(): int
    {
        return IPAddress::IP_VERSION_6;
    }

    /**
     * Returns the IPv6 colon-hexadecimal notation.
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

    private function iPv6MaskToByteArray(string $subnetMask): string
    {
        $addr = str_repeat("f", $subnetMask / 4);
        switch ($subnetMask % 4) {
            case 0:
                break;
            case 1:
                $addr .= "8";
                break;
            case 2:
                $addr .= "c";
                break;
            case 3:
                $addr .= "e";
                break;
        }
        $addr = str_pad($addr, 32, '0');
        return pack("H*", $addr);
    }

    /**
     * Maps the IPAddress object to an IPv6 address.
     */
    public function mapToIPv6(): static
    {
        return $this;
    }

    /**
     * Maps the IPAddress object to an IPv4 address.
     */
    public function mapToIPv4(): never
    {
        throw new Exception('Cannot convert IPv6 to IPv4!');
    }
}