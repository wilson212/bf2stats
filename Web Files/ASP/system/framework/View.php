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
use IOException;
use ArrayAccess;
use Exception;
use System\IO\File;
use System\IO\Path;

/**
 * Class View
 *
 * @package System
 */
class View
{
    /**
     * The left delimiter to use for parsing variables
     * @var string
     */
    protected $LDelim = '{';

    /**
     * The right delimiter to use for parsing variables
     * @var string
     */
    protected $RDelim = '}';

    /**
     * Assigned template variables and values
     * @var mixed[]
     */
    protected $variables = [];

    /**
     * An array of attached style sheets
     * @var string[]
     */
    protected static $stylesheets = [];

    /**
     * An array of attached scripts
     * @var array[] (location, type)
     */
    protected static $scripts = [];

    /**
     * An array of javascript Variables
     * @var array $name => $value
     */
    protected static $jsVariables = [];

    /**
     * Our parser loop limit to stop data holes
     * @var int
     */
    protected $iterations = 1;

    /**
     * Our current pages source
     * @var string
     */
    public $source;

    /**
     * @var bool Indicates whether to throw exceptions for parse errors
     */
    protected $throwParseErrors = false;

    /**
     * Array of template messages
     * @var array[] ('level', 'message', isClosable)
     */
    protected static $messages = [];

    /**
     * Adds a message to be displayed in the Global Messages container of the layout
     *
     * @param string $message The string message to display to the client
     * @param bool $isClosable Indicates whether the client is able to close this message
     */
    public static function ShowGlobalMessage($message, $isClosable = true): void
    {
        self::$messages[] = ['global', $message, $isClosable];
    }

    /**
     * Constructor
     *
     * @param string $viewName The name of the view file, no extension
     * @param string $moduleName The name of the module, whom owns the view file
     */
    public function __construct(protected $viewName, protected $moduleName = null)
    {
        $this->source = $this->loadView($this->viewName, $this->moduleName);
    }

    /**
     * Sets the number of parse iterations to do on the final HTML
     * output for PHP variables. Nested variables inside of other variables
     * will require multiple iterations to parse.
     *
     * @param int $i The number of iterations
     */
    public function setNumOfParseIterations($i): void
    {
        $this->iterations = (int)$i;
    }

    /**
     * Indicates whether to throw exceptions on parse errors,
     * or to silently fail.
     *
     * @param bool $throwParseErrors if true, undefined variables during
     *  parsing will result in an exception being thrown, otherwise the
     *  undefined variables will be ignored.
     */
    public function throwParseErrors($throwParseErrors): void
    {
        $this->throwParseErrors = $throwParseErrors;
    }

    /**
     * This method sets variables to be replace in the template system
     *
     * @param string $name The name of the variable
     * @param mixed $value The value of the variable
     *
     * @return $this
     */
    public function set($name, $value = null): static
    {
        if (is_array($name))
        {
            foreach ($name as $key => $v)
                $this->set($key, $v);
        }
        else {
            $this->variables[$name] = $value;
        }

        return $this;
    }

    /**
     * These method clears all the set variables for this view
     */
    public function clearVars(): void
    {
        $this->variables = [];
    }

    /**
     * Fetches all currently set variables
     *
     * @return mixed[]
     */
    public function getVars()
    {
        return $this->variables;
    }

    /**
     * Appends the header adding a css tag
     *
     * @param string $location The http location of the file
     */
    public function attachStylesheet($location): void
    {
        self::$stylesheets[] = $location;
    }

    /**
     * Appends the header adding a script tag for this view file
     *
     * @param string $location The http location of the file
     * @param string $type The script mime type, as it would be in the html script tag.
     */
    public function attachScript($location, $type = 'text/javascript'): void
    {
        self::$scripts[] = ['location' => $location, 'type' => $type];
    }

    /**
     * Sets a JavaScript variable that can be used globally in the view JavaScript file.
     *
     * @param string $name the name of the variable
     * @param mixed $value the value of the variable. Arrays will be converted to JSON format.
     * @param bool $quoteString indicates whether string values are to be quoted.
     */
    public function setJavascriptVar($name, $value, $quoteString = true): void
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } elseif (!is_numeric($value) && $quoteString) {
            $value = '"'. trim((string) $value, "\"'\t\n") .'"';
        }

        self::$jsVariables[$name] = $value;
    }

    /**
     * Adds a message to be displayed in the Global Messages container of the layout
     *
     * @param string $type The html class type ie: "error", "info", "warning" etc
     * @param string $message The string message to display to the client
     * @param bool $isClosable Indicates whether the client is able to close this message
     */
    public function displayMessage($type, $message, $isClosable = true): void
    {
        self::$messages[] = [$type, $message, $isClosable];
    }

    /**
     * This method displays the page. It loads the header, footer, and view of the page.
     *
     * @param bool $full Load header and footer as well?
     * @param bool $return if true, the output is returned instead of sent to the client.
     *
     * @internal param string $file The full path to the view file
     *
     * @return string|null returns the rendered source if $return is true
     */
    public function render($full = true, $return = false)
    {
        // Setup default Vars
        $header = '';
        $footer = '';

        // Full the header and footer vars
        if ($full == true)
        {
            // Load header and footer
            $header = $this->loadView('header');
            $footer = $this->loadView('footer');

            // if we have attached stylesheets, add them now
            $buffer = '';
            foreach (self::$stylesheets as $css)
                $buffer .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$css}\" media=\"screen\" />" . PHP_EOL;

            $header = str_replace($this->LDelim . "VIEW_CSS" . $this->RDelim, $buffer, $header);

            // Add variables
            $buffer = '';
            if (count(self::$jsVariables) > 0)
            {
                $buffer = "<script type=\"text/javascript\">" . PHP_EOL;
                foreach (self::$jsVariables as $key => $val)
                    $buffer .= "var {$key} = {$val};" . PHP_EOL;

                $buffer .= "</script>" . PHP_EOL;
            }

            $header = str_replace($this->LDelim . "JS_VARS" . $this->RDelim, $buffer, $header);

            // Add attached scripts
            $buffer = '';
            foreach (self::$scripts as $script)
                $buffer .= "<script type=\"{$script['type']}\" src=\"{$script['location']}\"></script>" . PHP_EOL;

            $header = str_replace($this->LDelim . "VIEW_JS" . $this->RDelim, $buffer, $header);

            // Display Global Messages
            $buffer = '';
            foreach (self::$messages as $message)
            {

                $buffer .= "<div class=\"alert {$message[0]}\">" . $message[1];
                $buffer .= $message[2] ? '<span class="close-bt"></span>' : '';
                $buffer .= "</div>" . PHP_EOL;
            }

            $header = str_replace($this->LDelim . "GLOBAL_MESSAGES" . $this->RDelim, $buffer, $header);
        }

        // Load the source
        $page = $header . $this->source . $footer;

        // Parse the template source
        $this->source = $this->parse($page, $this->variables);

        // Prepare the output
        $this->renderSource();

        // Are we supposed to return the source, or output it?
        if ($return)
        {
            return $this->source;
        }
        else
        {
            $this->output();
            return null;
        }
    }

    /**
     * This method outputs the page contents to the browser
     */
    protected function renderSource()
    {
        // Include template file if it exists
        $___file = ROOT . DS . 'frontend' . DS . 'template.php';
        if (file_exists($___file))
        {
            /** @noinspection PhpIncludeInspection */
            include($___file);
        }
        unset($___file);

        // use output buffering to catch the page. We do this so
        // we can catch php errors in the template
        ob_start();

        // Extract the variables so $this->variables[ $var ] becomes just " $var "
        extract($this->variables);

        // Eval the page
        eval('?>' . $this->source);

        // Capture the contents and call it a day
        $this->source = ob_get_contents();
        ob_end_clean();
    }

    /**
     * This method outputs the page contents to the browser
     */
    protected function output()
    {
        echo $this->source;
    }

    /**
     * Checks whether there is a template file and if its readable. Stores contents of file if read is successfull
     *
     * @param string $viewName The name of the view file, no extension
     * @param string $moduleName The name of the module, whom owns the view file
     *
     * @throws IOException
     *
     * @return string
     */
    protected function loadView(string $viewName, $moduleName = null)
    {
        $file = (empty($moduleName))
            ? Path::Combine(ROOT, "frontend", "views", $viewName . '.tpl')
            : Path::Combine(ROOT, "frontend", "modules", $moduleName, "views", $viewName . '.tpl');

        // Make sure the file exists!
        if (!file_exists($file)) {
            throw new IOException("Unable to locate view file '$file'");
        }

        // Get the file contents and return
        return File::ReadAllText($file);
    }

    /**
     * This method loops through all the specified variables, and replaces
     * the Pseudo blocks that contain variable names
     *
     * @param string $source The source string to parse
     * @param mixed[] $variables The variables to parse in the string
     *
     * @return string
     */
    protected function parse($source, $variables)
    {
        // store the vars into $data, as its easier then $this->variables
        $count = 0;

        // Do a search and destroy or pseudo blocks... keep going till we replace everything
        do
        {
            // If we don't replace something in the current iteration, then we'll break;
            $replaced_something = false;

            // Loop through the data and catch arrays
            foreach ($variables as $key => $value)
            {
                // If $value is an array, we need to process it as so
                if (is_array($value) || $value instanceof ArrayAccess)
                {
                    // First, we check for array blocks (Foreach blocks), you do so by checking: {/key}
                    // .. if one exists we preg_match the block
                    if (str_contains($source, $this->LDelim . '/' . $key . $this->RDelim))
                    {
                        // Create our array block regex
                        $regex = $this->LDelim . $key . $this->RDelim . "(.*)" . $this->LDelim . '/' . $key . $this->RDelim;

                        // Match all of our array blocks into an array, and parse each individually
                        preg_match_all("~" . $regex . "~iUs", $source, $matches, PREG_SET_ORDER);
                        foreach ($matches as $match)
                        {
                            // Parse pair: Source, Match to be replaced, With what are we replacing?
                            $replacement = $this->parsePair($match[1], $value);
                            if ($replacement === "_PARSER_false_") {
                                continue;
                            }

                            // Main replacement
                            $source = str_replace($match[0], $replacement, $source);
                            $replaced_something = true;
                        }
                    }

                    // Now that we are done checking for blocks, Create our array key identifier
                    $key .= ".";

                    // Next, we check for nested array blocks, you do so by checking for: {/key.*}.
                    // ..if one exists we preg_match the block
                    if (str_contains($source, $this->LDelim . "/" . $key))
                    {
                        // Create our regex
                        $regex = $this->LDelim . $key . "(.*)" . $this->RDelim . "(.*)" . $this->LDelim . '/' . $key . "(.*)" . $this->RDelim;

                        // Match all of our array blocks into an array, and parse each individually
                        preg_match_all("~" . $regex . "~iUs", $source, $matches, PREG_SET_ORDER);
                        foreach ($matches as $match)
                        {
                            // Parse pair: Source, Match to be replaced, With what are we replacing?
                            $replacement = $this->parsePair($match[2], $this->parseArray($match[1], $value));
                            if ($replacement === "_PARSER_false_") {
                                continue;
                            }

                            // Check for a false reading
                            $source = str_replace($match[0], $replacement, $source);
                            $replaced_something = true;
                        }
                    }

                    // Lastly, we check just plain arrays. We do this by looking for: {key.*}
                    // .. if one exists we preg_match the array
                    if (str_contains($source, $this->LDelim . $key))
                    {
                        // Create our regex
                        $regex = $this->LDelim . $key . "(.*)" . $this->RDelim;

                        // Match all of our arrays into an array, and parse each individually
                        preg_match_all("~" . $regex . "~iUs", $source, $matches, PREG_SET_ORDER);
                        foreach ($matches as $match)
                        {
                            // process the array, If we got a false array parse, then skip the rest of this loop
                            $replacement = $this->parseArray($match[1], $value);
                            if ($replacement === "_PARSER_false_") {
                                continue;
                            }

                            // If our replacement is a array, it will cause an error, so just return "array"
                            if (is_array($replacement) || $value instanceof ArrayAccess) {
                                $replacement = "array";
                            }

                            // Main replacement
                            $source = str_replace($match[0], $replacement ?? '', $source);
                            if ($replacement != $match[0]) {
                                $replaced_something = true;
                            }
                        }
                    }
                }
            }

            // Now parse singles. We do this last to catch variables that were
            // inside array blocks...
            foreach ($variables as $key => $value)
            {
                // We don't handle arrays here
                if (is_array($value) || $value instanceof ArrayAccess) {
                    continue;
                }

                // Find a match for our key, and replace it with value
                $match = $this->LDelim . $key . $this->RDelim;
                if (str_contains($source, $match))
                {
                    $source = str_replace($match, $value, $source);
                    $replaced_something = true;
                }
            }

            // If we did not replace anything, quit
            if (!$replaced_something) {
                break;
            }

            // Raise the counter
            ++$count;
        } while ($count < $this->iterations);

        // Return the parsed source
        return $source;
    }

    /**
     * Parses a string array such as {user.userinfo.username}
     *
     * @param string $key The full un-parsed array ( { something.else} )
     * @param mixed[] $array The actual array that holds the value of $key
     *
     * @return mixed Returns the parsed value of the array key
     */
    protected function parseArray($key, $array)
    {
        // Check to see if this is even an array first
        if (!is_array($array) && !($array instanceof ArrayAccess)) {
            return $array;
        }

        // Check if this is a multi-dimensional array
        if (str_contains($key, '.')) {
            $args = explode('.', $key);
            $count = count($args);
            for ($i = 0; $i < $count; $i++)
            {
                if (!isset($array[$args[$i]]))
                {
                    // Are we throwing errors?
                    if ($this->throwParseErrors)
                    {
                        throw new Exception("Parser: Undefined array index ($i) in ($key)");
                    }

                    return "_PARSER_false_";
                }
                elseif ($i === $count - 1)
                {
                    return $array[$args[$i]];
                }
                else
                {
                    $array = $array[$args[$i]];
                }
            }
        } elseif (array_key_exists($key, $array)) {
            // Check if variable exists in $array
            return $array[$key];
        }

        // Tell the requester that the array doesn't exist
        return "_PARSER_false_";
    }

    /**
     * Parses array blocks (  {key} ,,, {/key} ), acts like a foreach loop
     *
     * @param string $match The preg_match of the block {key} (what we need) {/key}
     * @param mixed[] $val The array that contains the variables inside the blocks
     *
     * @return string Returns the parsed foreach loop block
     */
    protected function parsePair($match, $val): string|array
    {
        // Init the empty main block replacement
        $final_out = '';

        // Make sure we are dealing with an array!
        if (!is_array($val) || !is_string($match)) {
            return "_PARSER_false_";
        }

        // Remove nested vars, nested vars are for outside vars
        if (str_contains($match, $this->LDelim . $this->LDelim))
        {
            $match = str_replace(
                [$this->LDelim . $this->LDelim, $this->RDelim . $this->RDelim],
                ["<<!~", "~!>>"],
                $match
            );
        }

        // Define out loop number
        $i = 0;

        // Process the block loop here, We need to process each array $val
        foreach ($val as $key => $value)
        {
            // if value isn't an array, then we just replace {value} with string
            if (is_array($value) || $value instanceof ArrayAccess) {
                // Parse our block. This will catch nested blocks and arrays as well
                $block = $this->parse($match, $value);
            } else {
                // Just replace {value}, as we are dealing with a string
                $block = str_replace('{value}', $value, $match);
            }

            // Setup a few variables to tell what loop number we are on
            if (str_contains($block, "{iteration."))
            {
                $block = str_replace(
                    ["{iteration.key}", "{iteration.id}", "{iteration.count}", "{iteration.length}"],
                    [$key, $i, $i + 1, count($val)],
                    $block
                );
            }

            // Add this finished block to the final return
            $final_out .= $block;
            ++$i;
        }

        // Return nested vars
        if (str_contains($final_out, "<<!~"))
        {
            $final_out = str_replace(
                ["<<!~", "~!>>"],
                [$this->LDelim, $this->RDelim],
                $final_out
            );
        }

        return $final_out;
    }
}