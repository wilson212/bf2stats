<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */

namespace System\Collections;
use IteratorAggregate;
use ArrayAccess;
use Countable;
use Serializable;
use ArrayIterator;
use Exception;

/**
 * Represents a collection of keys and values.
 *
 * What sets the Dictionary apart over an array is that the Dictionary will throw
 * exceptions instead of outputting PHP errors, allowing the developer more control
 * over erroneous operations.
 *
 * The Dictionary class also supports case-insensitive key lookups and read-only
 * enforcement. Read-Only enforcement can only enforce that no items are added or removed
 * from this collection, not the changing of values!
 *
 * You can access and add items to the collection using the "add", "itemAt", and "remove"
 * methods, or you can use this object like an array:
 * <ul>
 *    <li> $dictionary[$key] = $value </li>
 *    <li> unset($dictionary[$key]) </li>
 *    <li> if(isset($dictionary[$key])) </li>
 *    <li> $numItems = count($dictionary) </li>
 *    <li> foreach($dictionary as $item) </li>
 * </ul>
 */
class Dictionary implements IteratorAggregate, ArrayAccess, Countable, Serializable
{
    /**
     * @var mixed[] Data Container.
     */
    private array $data = [];

    /**
     * @var int The index count of the data container
     */
    protected $size = 0;

    /**
     * Constructor
     *
     * @param bool $isReadOnly Indicates whether this Dictionary collection will be readonly.
     * @param array $items Default items to add to this Dictionary
     * @param bool $caseSensitive Indicates whether the comparison of keys is case sensitive.
     */
    public function __construct(protected $isReadOnly = false, array $items = [], protected $caseSensitive = true)
    {
        // Add initialization data is set
        if ($items !== [])
        {
            // Set internal data container
            $this->data = ($this->caseSensitive) ? $items : array_change_key_case($items, CASE_LOWER);

            // Set internal size counter
            $this->size = count($items);
        }
    }

    /**
     * Adds an item to the dictionary
     *
     * @param string $key The item key
     * @param mixed $value The value of the item key
     *
     * @throws Exception if the collection is read-only
     */
    public function add($key, $value): void
    {
        if ($this->isReadOnly) {
            throw new Exception("Unable to add item to Dictionary. The current Dictionary object is set to read-only.");
        }

        // Add item
        if (!empty($key) || $key === 0)
        {
            // Lowercase key if we are in case-insensitive mode
            if (!$this->caseSensitive && !is_numeric($key)) {
                $key = strtolower($key);
            }

            $this->data[$key] = $value;
        }
        else {
            $this->data[] = $value;
        }

        ++$this->size;
    }

    /**
     * Determines whether the dictionary contains the specified key
     *
     * @param mixed $key The item key
     */
    public function containsKey($key): bool
    {
        // Lowercase key if we are in case-insensitive mode
        if (!$this->caseSensitive && !is_numeric($key)) {
            $key = strtolower((string) $key);
        }

        return array_key_exists($key, $this->data);
    }

    /**
     * Determines whether the dictionary contains the specified key.
     *
     * DOES NOT DO CASE IN-SENSITIVE CHECKS!
     *
     * @param mixed $key The item key
     */
    private function _containsKey($key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Determines whether the dictionary contains a value
     *
     * @param mixed $item The value to search for
     */
    public function containsValue($item): bool
    {
        return (($index = array_search($item, $this->data, true)) !== false);
    }

    /**
     * Returns All the dictionary keys
     *
     * @return string[]
     */
    public function getKeys()
    {
        return array_keys($this->data);
    }

    /**
     * Returns All the dictionary values
     *
     * @return mixed[]
     */
    public function getValues()
    {
        return array_values($this->data);
    }

    /**
     * Removes all items from the dictionary
     *
     * @throws Exception
     */
    public function clear(): void
    {
        if ($this->isReadOnly) {
            throw new Exception("Unable to clear Dictionary items. The current Dictionary object is set to read-only.");
        }

        $this->data = [];
        $this->size = 0;
    }

    /**
     * Gets the value associated with the specified key
     *
     * @param string $key The item's key
     *
     * @return mixed Returns the item of the specified index
     * @throws Exception if the $key is not present in the dictionary
     */
    public function itemAt($key)
    {
        // Lowercase key if we are in case-insensitive mode
        if (!$this->caseSensitive && !is_numeric($key)) {
            $key = strtolower($key);
        }

        // Check if key exists in the collection
        if (!$this->_containsKey($key)) {
            throw new Exception("The given key was not present in the dictionary: {$key}");
        }

        return $this->data[$key];
    }

    /**
     * Gets the value associated with the specified key.
     *
     * @param mixed $key The key of the value to get.
     * @param mixed $value Contains the value associated with the specified key if the key is found; otherwise null
     *
     * @return bool true if the Dictionary contains an element with the specified key; otherwise, false.
     */
    public function tryGetValue($key, &$value): bool
    {
        // Lowercase key if we are in case-insensitive mode
        if (!$this->caseSensitive && !is_numeric($key)) {
            $key = strtolower((string) $key);
        }

        // Check if key exists in the collection
        if (!$this->_containsKey($key))
        {
            $value = null;
            return false;
        }

        $value = $this->data[$key];
        return true;
    }

    /**
     * Gets the value associated with the specified key, or returns the specified
     * default value if the Dictionary does not contain the specified key.
     *
     * @param mixed $key The key of the value to get.
     * @param mixed $default The default value to return in the specified key does not exist.
     *
     * @return mixed
     */
    public function getValueOrDefault($key, $default)
    {
        // Lowercase key if we are in case-insensitive mode
        if (!$this->caseSensitive && !is_numeric($key)) {
            $key = strtolower((string) $key);
        }

        // Check if key exists in the collection
        return ($this->_containsKey($key)) ? $this->data[$key] : $default;
    }

    /**
     * Removes the value with the specified key from the Dictionary
     *
     * @param $key
     *
     * @throws Exception
     * @internal param mixed $item The item value to search for
     * @return bool true if the item was removed, otherwise false
     */
    public function remove($key): bool
    {
        if ($this->isReadOnly) {
            throw new Exception("Unable to remove item to Dictionary. The current Dictionary object is set to read-only.");
        }

        // Lowercase key if we are in case-insensitive mode
        if (!$this->caseSensitive && !is_numeric($key)) {
            $key = strtolower((string) $key);
        }

        // Check if key exists in the collection
        if ($this->_containsKey($key))
        {
            unset($this->data[$key]);
            --$this->size;
            return true;
        }

        return false;
    }

    /**
     * Returns the list as an array
     * @return mixed[]
     */
    public function toArray()
    {
        return $this->data;
    }

    // === Interface / Abstract Methods === //

    /**
     * Returns the number of items in the list
     * This method is required by the interface Countable.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->size;
    }

    /**
     * Returns whether the specified item key exists in the container
     * This method is required by the interface ArrayAccess.
     *
     * @param string $key The item key to check for
     *
     * @return bool
     */
    public function offsetExists($key): bool
    {
        // Lowercase key if we are in case-insensitive mode
        if (!$this->caseSensitive && !is_numeric($key)) {
            $key = strtolower($key);
        }

        // Check if key exists in the collection
        return $this->_containsKey($key);
    }

    /**
     * Returns the item value of the specified key.
     * This method is required by the interface ArrayAccess.
     *
     * @param string $key The item key
     *
     * @return mixed
     */
    public function offsetGet($key): mixed
    {
        return $this->itemAt($key);
    }

    /**
     * Sets the item with the specified key.
     * This method is required by the interface ArrayAccess.
     *
     * @param string $key The item key to set
     * @param mixed $value The item value
     */
    public function offsetSet($key, $value): void
    {
        $this->add($key, $value);
    }

    /**
     * Removes the item with the specified key.
     * This method is required by the interface ArrayAccess.
     *
     * @param string $key The item key
     */
    public function offsetUnset($key): void
    {
        $this->remove($key);
    }

    /**
     * Serializes the data, and returns it.
     * This method is required by the interface Serializable.
     * This method exists only for PHP7 support.
     *
     * @return string The serialized string
     */
    public function serialize(): ?string
    {
        return serialize($this->data);
    }

    public function __serialize(): array
    {
        return [
            'data' => $this->data
        ];
    }

    /**
     * Unserializes the data, and sets up the storage in this container
     * This method is required by the interface Serializable.
     * This method exists only for PHP7 support.
     *
     * @param string $data
     */
    public function unserialize($data): void
    {
        $this->data = unserialize($data);
    }

    public function __unserialize($data): void
    {
        $this->$data = $data['data'];
    }

    /**
     * Returns the ArrayIterator of this object
     * This method is required by the interface IteratorAggregate.
     *
     * @return string The serialized string
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->data);
    }
}