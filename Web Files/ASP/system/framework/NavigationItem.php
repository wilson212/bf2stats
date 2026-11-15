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
 * Class NavigationItem
 * @package System
 */
class NavigationItem
{
    /**
     * @var array Contains the sub menu links
     */
    public $sublinks = [];

    /**
     * NavigationItem constructor.
     *
     * @param string $text
     * @param string $href
     * @param string $icon
     * @param bool $isActive
     */
    public function __construct(public $text, public $href, public $icon, public $isActive)
    {
    }

    /**
     * Appends a sub link in this navigation group
     *
     * @param string $href The location this link points to
     * @param string $text The text for the link
     */
    public function append($href, $text): void
    {
        $this->sublinks[$href] = $text;
    }

    /**
     * Returns the HTML representation of this NavigationItem
     */
    public function toHtml(): string
    {
        $html = "\t<li";
        $html .= (($this->isActive) ? ' class="active">' : '>');
        $html .= "\n\t\t";

        // Add link
        $html .= '<a href="'. $this->href .'"><i class="'. $this->icon .'"></i> '. $this->text .'</a>';

        if (!empty($this->sublinks))
        {
            $html .= "\n\t\t\t<ul";
            $html .= (($this->isActive) ? '>' : ' class="closed">');

            foreach ($this->sublinks as $href => $text)
            {
                $html .= "\n\t\t\t\t";
                $html .= '<li><a href="'. $href .'">'. $text .'</a></li>';
            }

            $html .= "\n\t\t\t</ul>";
        }
        return $html . "\n\t</li>";
    }
}