<?php
/* 
| --------------------------------------------------------------
| BF2 Statistics Admin Util
| --------------------------------------------------------------
| Original Author: The Shadow
| Author:       Steven Wilson 
| Copyright:    Copyright (c) 2012
| License:      GNU GPL v3
| ---------------------------------------------------------------
| Class: Config()
| ---------------------------------------------------------------
|
*/

class Config 
{
	protected static $data = array();
	protected static $configFile;
	
/*
| ---------------------------------------------------------------
| Constructer
| ---------------------------------------------------------------
|
*/
	public static function Init() 
	{
        if(empty(self::$configFile))
        {
            // Load the config File
            self::$configFile = SYSTEM_PATH . DS . 'config'. DS . 'config.php';
            if( !self::Load() )
            {
                throw new Exception('Failed to load config file!');
            }
        }
	}
	
/*
| ---------------------------------------------------------------
| Method: get()
| ---------------------------------------------------------------
|
| Returns the variable ($key) value in the config file.
|
| @Param: (String) $key - variable name. Value is returned
| @Return: (Mixed) May return NULL if the var is not set
|
*/
    public static function Get($key) 
    {
        // Check if the variable exists
        if(isset(self::$data[$key])) 
        {
            return self::$data[$key];
        }
        return NULL;
    }
	
/*
| ---------------------------------------------------------------
| Method: getAll()
| ---------------------------------------------------------------
|
| Returns all variable keys and values in the config file.
|
| @Return: (Array)
|
*/
    public static function FetchAll() 
    {
        return self::$data;
    }

/*
| ---------------------------------------------------------------
| Method: set()
| ---------------------------------------------------------------
|
| Sets the variable ($key) value. If not saved, default value
| will be returned as soon as page is re-loaded / changed.
|
| @Param: (String or Array) $key - variable name to be set
| @Param: (Mixed) $value - new value of the variable
| @Return: (None)
|
*/
    public static function Set($key, $val = false) 
    {
        // If we have array, loop through and set each
        if(is_array($key))
        {
            foreach($key as $k => $v)
            {
                self::$data[$k] = $v;
            }
        }
        else
        {
            self::$data[$key] = $val;
        }
    }

/*
| ---------------------------------------------------------------
| Method: Save()
| ---------------------------------------------------------------
|
| Saves all set config variables to the config file, and makes 
| a backup of the current config file
|
| @Return: (Bool) TRUE on success, FALSE otherwise
|
*/
	public static function Save() 
	{
		$cfg  = "<?php\n";
		$cfg .= "/***************************************\n";
		$cfg .= "*  Battlefield 2 Private Stats Config  *\n";
		$cfg .= "****************************************\n";
		$cfg .= "* All comments have been removed from  *\n";
		$cfg .= "* this file. Please use the Web Admin  *\n";
		$cfg .= "* to change values.                    *\n";
		$cfg .= "***************************************/\n";
		
		// Get each of the new set variables
		foreach( self::$data as $key => $val ) 
		{
			// If the value is numeric, then put in a "clean" value
			if (is_numeric($val)) 
			{
				$cfg .= "\$$key = " . $val . ";\n";
			} 
			
			// Check for array values (admin_hosts, game_hosts, and stats_local_pids)
			elseif($key == 'admin_hosts' || $key == 'game_hosts' || $key == 'stats_local_pids') 
			{
				if(!is_array($val)) 
				{
					$val_r = explode("\n", $val);
				}
				else
				{
					$val_r = $val;
				}
				$val_s = "";
				foreach($val_r as $item) 
				{
					$val_s .= "'".trim($item)."',";
				}
				$cfg .= "\$$key = array(" . substr($val_s, 0, -1) . ");\n";
			} 
			
			// If the value is not numeric or an array, then we need to put the new value in quotes
			else 
			{
				$cfg .= "\$$key = '" . addslashes( $val ) . "';\n";
			}
		}
		$cfg .= "?>";
		
		// Copy the current config file for backup, and write the new config values to the new config
        copy( self::$configFile, self::$configFile .'.bak' );
        if(file_put_contents( self::$configFile, $cfg )) 
        {
            return TRUE;
        } 
        else 
        {
            return FALSE;
        }
	}
	
/*
| ---------------------------------------------------------------
| Method: Load()
| ---------------------------------------------------------------
|
| Load the config file, and adds its defined variables to the $data
|   array
|
| @Return: TRUE on success, FALSE otherwise
|
*/
	protected static function Load() 
	{
		if(file_exists( self::$configFile )) 
		{
			include_once( self::$configFile );
			$vars = get_defined_vars();
			foreach( $vars as $key => $val ) 
			{
				if($key != 'this' && $key != 'data') 
				{
					self::$data[$key] = $val;
				}
			}
			return true;
		} 
		else 
		{
			return false;
		}
	}
}

Config::Init();
?>