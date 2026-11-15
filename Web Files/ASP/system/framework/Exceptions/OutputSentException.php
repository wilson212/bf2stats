<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */

/**
 * Output Sent Exception, Thrown when headers have already been set, and a Response method is called
 *
 * @package     Core
 * @subpackage  Exceptions
 */
class OutputSentException extends Exception {}