<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */

namespace System\Cache;

interface ICacheDriver
{
    /**
     * Gets an item from the cache with the specified name, or a
     * new item if the cached item did not exist ot was expired
     *
     * @param string $key The cached item key
     *
     * @return CacheItem
     */
    public function getItem($key);

    /**
     * Saves an item to the cache
     *
     *
     * @return void
     */
    public function save(CacheItem $item);

    /**
     * Indicates whether the item key is cached
     *
     * @param string $key The cached item key
     *
     * @return bool
     */
    public function hasItem($key);

    /**
     * Deletes the cached item key is it exists
     *
     * @param string $key The cached item key
     *
     * @return void
     */
    public function deleteItem($key);
}