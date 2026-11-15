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


interface iIPAddress
{
    /**
     * The isLoopback method compares address to Loopback and returns true if the
     *  two IP addresses are the same.
     *
     * In the case of IPv4, that the IsLoopback method returns true for any IP address
     *  of the form 127.X.Y.Z (where X, Y, and Z are in the range 0-255), not just
     *  Loopback (127.0.0.1).
     *
     * @return bool
     */
    public function isLoopback();

    /**
     * Indicates whether this address falls under the supplied CIDR
     *
     * @param string|iIPAddress $address
     *
     * @return bool
     */
    public function isInCidr($address);

    /**
     * Compares the current IPAddress instance with the comparing parameter and returns true
     *  if the two instances contain the same IP address.
     *
     * @param string|iIPAddress $Ip The IP address to compare with
     *
     * @return bool
     */
    public function equals($Ip);

    /**
     * Returns the IP address type (@see IPAddress::IP_VERSION_*)
     *
     * @return int
     */
    public function getType();

    /**
     * Maps the IPAddress object to an IPv6 address.
     *
     * @return IPv6Address
     */
    public function mapToIPv6();

    /**
     * Maps the IPAddress object to an IPv4 address.
     *
     * @return IPv4Address
     */
    public function mapToIPv4();

    /**
     * Returns either IPv4 dotted-quad or IPv6 colon-hexadecimal notation.
     *
     * @return string
     */
    public function toString();
}