<?php

/*
 * This file is part of the Keygen package.
 *
 * (c) Glad Chinda <gladxeqs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace System\Keygen\Generators;

use System\Keygen\Generator;

class TokenGenerator extends Generator
{
	/**
     * Generates a random key.
     *
     * @param numeric $length
     */
    protected function keygen($length): string
	{
		$token = '';
		$tokenlength = round($length * 3 / 4);

		for ($i = 0; $i < $tokenlength; ++$i) {
			$token .= chr(random_int(32,1024));
		}

		$token = base64_encode(str_shuffle($token));

		return substr($token, -$length);
	}
}
