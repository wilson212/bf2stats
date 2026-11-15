<?php

/*
 * This file is part of the Keygen package.
 *
 * (c) Glad Chinda <gladxeqs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace System\Keygen\Traits;

use InvalidArgumentException;
use System\Keygen\AbstractGenerator;

trait MutableGenerator
{
	use FlattenArguments;

	/**
	 * A collection of mutable attributes.
	 * 
	 * @var array
	 */
	protected $mutables = [];

	/**
     * Add mutable attributes to the mutables collection
     *
     * @param mixed $props
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function mutable($props)
	{
		$props = call_user_func_array([$this, 'flattenArguments'], func_get_args()); 
		$collect = $unknown = [];

		foreach ($props as $prop) {
			if (!property_exists(AbstractGenerator::class, $prop)) {
                $unknown[] = $prop;
                continue;
            }

			$collect[] = $prop;
		}

		if ($unknown !== []) {

			throw new InvalidArgumentException(sprintf("Cannot add unknown %s to mutables collection ('%s').", (count($unknown) > 1) ? 'properties' : 'property', implode("', '", $unknown)));
			
		}
		
		$this->mutables = array_merge(array_diff($this->mutables, $collect), $collect);
		
		return $this;
	}

	/**
	 * Remove attributes from the mutables collection
	 * 
	 * @param mixed $props
	 * @return $this
	 */
	public function immutable($props)
	{
		$props = call_user_func_array([$this, 'flattenArguments'], func_get_args());
		
		$this->mutables = array_diff($this->mutables, $props);
		
		return $this;
	}
}
