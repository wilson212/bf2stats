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
 * Class Navigation
 * @package System
 */
class Navigation
{
    /**
     * @var NavigationItem[]
     */
    protected $items = [];

    /**
     * Appends a Navigation Item to the current navigation set.
     */
    public function append(NavigationItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * Generates the un-ordered navigation list
     */
    public function toHtml(): string
    {
        $html = '<ul>';

        foreach ($this->items as $item)
        {
            $html .= "\n";
            $html .= $item->toHtml();
        }

        $html .= "\n";

        return $html . '</ul>';
    }
}