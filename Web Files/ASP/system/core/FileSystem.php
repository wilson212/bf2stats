<?php
/**
 * A class built to easily manage files and directories
 *
 * @author      Steven Wilson 
 */
class FileSystem
{
    /**
     * This method is used to return whether a file OR directory is writable.
     *
     * @param string $path The complete path to the file or directory
     * @return bool
     */
    public static function IsWritable($path) 
    {
        // Correct path
        $path = str_replace(array('/', '\\'), DS, $path);
        
        // Check if givin param is a path
        if(is_dir($path))
        {
            // Fix path, and Create a tmp file
            if($path[strlen($path)-1] != DS) $path = $path . DS;
            $file = $path . uniqid(mt_rand()) .'.tmp';
            
            // check tmp file for read/write capabilities
            $handle = @fopen($file, 'a');
            if ($handle === false) 
                return false;
            
            // Close the folder and remove the temp file
            fclose($handle);
            unlink($file);
            return true;
        }
        else
        {
            // Make sure the file exists
            if( !file_exists($path) ) return false;
            
            // Attempt to open the file, and read contents
            $handle = @fopen($path, 'a');
            if($handle === false) 
                return false;
            
            // Close the file, return true
            fclose($handle);
            return true;
        }
    }
    
    /**
     * This method is used to return whether a file OR directory is
     * readable and can be opened.
     *
     * @param string $path The complete path to the file or directory
     * @return bool
     */
    public static function IsReadable($path) 
    {
        // Correct path
        $path = str_replace(array('/', '\\'), DS, $path);
        
        // Check if givin param is a path
        if(is_dir($path))
        {
            // Open the dir, and base read off of that
            $handle = @opendir($path);
            if($handle === false)
                return false;
            
            // Close the dir, and return true
            closedir($handle);
            return true;
        }
        else
        {
            // Make sure the file exists
            if( !file_exists($path) ) 
                return false;
            
            // Attempt to open the file, and read contents
            $handle = @fopen($path, 'r');
            if($handle === false)
                return false;
            
            // Close the file, return true
            fclose($handle);
            return true;
        }
    }
    
    /**
     * Creates a new directory
     *
     * @param string $path  The complete path to the new directory
     * @param int $chmod The desired chmod on the folder
     * @return bool Returns true if the directory was created successfully.
     */
    public static function CreateDir($path, $chmod = 0777)
    {
        // Correct path
        $path = str_replace(array('/', '\\'), DS, $path);
        
        // Check for an existing directory
        if(is_dir($path))
            return true;

        // Get current directory mask
        $oldumask = umask(0);
        $result = mkdir($path, $chmod, true);
        
        // Return to the old file mask, and return true
        umask($oldumask);
        return (bool) $result;
    }
    
    /**
     * Removes a directory. You must use caution
     * with this method as its recursive, and will delete all sub files
     * and directories
     *
     * @param string $path  The complete path to the directory
     * @return bool Returns true if the directory was removed successfully.
     */
    public static function RemoveDir($path)
    {
        // Correct path
        $path = str_replace(array('/', '\\'), DS, $path);

        // Make sure we have a path, and not a file
        if(is_dir($path))
        {
            // Make sure our path is correct
            if($path[strlen($path)-1] != DS) $path = $path . DS;
            
            // Open the directory
            $handle = @opendir($path);
            if ($handle === false) return false;
            
            // remove all sub directoires and files
            while(false !== ($f = readdir($handle)))
            {
                // Skip "." and ".." directories
                if($f == "." || $f == "..") continue;

                // make sure we establish the full path to the file again
                $file = $path . $f;
                
                // If is directory, call this method again to loop and delete ALL sub dirs.
                if(is_dir($file)) 
                {
                    self::RemoveDir($file);
                }
                else 
                {
                    self::DeleteFile($file);
                }
            }
            
            // Close our path
            closedir($handle);
            
            // Clear stats cache and remove our current directory
            $result = rmdir($path);
            clearstatcache();
            return $result;
        }
        
        return false;
    }
    
    /**
     * Creates a new file.
     *
     * @param string $file The complete path to the new file
     * @param string|mixed[] $contents The contents to place in the file.
     *   If contents are an array, they will be serialized using the php
     *   function <i>serialize()</i>. Default value is null
     * @return bool Returns true if the file was created successfully
     */
    public static function CreateFile($file, $contents = null)
    {
        // Correct path
        $file = str_replace(array('/', '\\'), DS, $file);
        
        // Add trace for debugging
        // \Debug::trace("Creating file '{$file}'", __FILE__, __LINE__);

        // Attempt to create the file
        $handle = @fopen($file, 'w+');
        if($handle)
        {
            // If contents are an array, then serialize them
            if(is_array($contents)) $contents = serialize($contents);
            
            // only add file contents if they are not null!
            if(!empty($contents))
            {
                fwrite($handle, $contents);
                fclose($handle);
            }
            
            // Return true if we are here
            return true;
        }
        
        // Add trace for debugging
        // \Debug::trace("Creation of file '{$file}' failed.", __FILE__, __LINE__);
        return false;
    }
    
    /**
     * Deletes a file
     *
     * @param string $file The complete path to the file
     * @return bool Returns true if the file was deleted successfully
     */
    public static function DeleteFile($file)
    {
        // Correct path
        $file = str_replace(array('/', '\\'), DS, $file);
        
        // Attempt to delete the file
        return ( @unlink($file) );
    }
    
    /**
     * Lists an array of files in a directory
     *
     * @param string $path The complete path to the directory
     * @return string[] Returns an array of all the filenames in the directory
     */
    public static function ListFiles($path)
    {
        // Correct path
        $path = str_replace(array('/', '\\'), DS, $path);
        
        // Make sure we have a path, and not a file
        if(is_dir($path))
        {
            // Make sure our path is correct
            if($path[strlen($path)-1] != DS) $path = $path . DS;
            
            // Open the directory
            $handle = @opendir($path);
            if ($handle === false) return false;
            
            // Files array
            $files = array();
            
            // Loop through each file
            while(false !== ($f = readdir($handle)))
            {
                // Skip "." and ".." directories
                if($f == "." || $f == "..") continue;

                // make sure we establish the full path to the file again
                $file = $path . $f;
                
                // If is directory, call this method again to loop and delete ALL sub dirs.
                if( !is_dir($file) ) 
                {
                    $files[] = $f;
                }
            }
            
            // Close our path
            closedir($handle);
            return $files;
        }
        return false;
    }
    
    /**
     * Lists an array of folders in a directory
     *
     * @param string $path The complete path to the directory
     * @return array
     */
    public static function ListFolders($path)
    {
        // Correct path
        $path = str_replace(array('/', '\\'), DS, $path);
        
        // Make sure we have a path, and not a file
        if(is_dir($path))
        {
            // Make sure our path is correct
            if($path[strlen($path)-1] != DS) $path = $path . DS;
            
            // Open the directory
            $handle = @opendir($path);
            if ($handle === false) return false;
            
            // Folders array
            $folders = array();
            
            // Loop through each file
            while(false !== ($f = readdir($handle)))
            {
                // Skip "." and ".." directories
                if($f == "." || $f == "..") continue;

                // make sure we establish the full path to the file again
                $file = $path . $f;
                
                // If is directory, call this method again to loop and delete ALL sub dirs.
                if(is_dir($file)) 
                {
                    $folders[] = $f;
                }
            }
            
            // Close our path
            closedir($handle);
            return $folders;
        }
        return false;
    }
    
    /**
     * Copies the contents of a source file, to another file
     *
     * @param string $src The complete path to the source file
     * @param string $dest The complete path to the destination file
     * @return bool Returns true on success, or false
     */
    public static function Copy($src, $dest)
    {
        // Correct paths
        $src = str_replace(array('/', '\\'), DS, $src);
        $dest = str_replace(array('/', '\\'), DS, $dest);
        
        // Make sure the src file exists
        if( !file_exists($src) )
            return false;
        
        // Copy the file
        return (copy($src, $dest));
    }
    
    /**
     * Rename's a file
     *
     * @param string $src The complete path to the source file / folder
     * @param string $dest The complete path to the destination file / folder
     * @return bool Returns true on success, or false
     */
    public static function Rename($src, $dest)
    {
        // Correct paths
        $src = str_replace(array('/', '\\'), DS, $src);
        $dest = str_replace(array('/', '\\'), DS, $dest);
        
        // Make sure the src file exists
        if( !file_exists($src) )
            return false;
        
        // Rename the file
        return rename($src, $dest);
    }
    
    /**
     * Determines if the path given is a folder OR a file,
     * and removes it accordinly using this class's RemoveDir and
     * DeleteFile methods
     *
     * @param string $path The complete path to the source file / folder
     * @param string[] $files An array of files / folders to remove
     * @return bool Returns true on success, or false
     */
    public static function Delete($path, $files = array())
    {
        // Correct path
        $path = str_replace(array('/', '\\'), DS, $path);
        
        // Make sure the Dir exists
        if(is_dir($path))
        {
            // Make sure our path is correct
            if($path[strlen($path)-1] != DS) $path = $path . DS;

            // Check to see if we are just removing file or what :O
            if(!empty($files))
            {
                foreach($files as $f)
                {
                    // Attempt to delete the file, return false if even 1 fails
                    if( !self::Delete( $path . $f ) )
                        return false;
                }
                
                // End of loop, return true
                return true;
            }
            else
            {
                // Remove the whole dir
                return self::RemoveDir($path);
            }
        }
        else
        {
            return self::DeleteFile($path);
        }
    }
}