<?php
/**
 * Autoloader class provides methods for loading classes
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 */
class Autoloader
{
    /**
     * This method registers loadClass method as an autoload function.
     */
    public static function enable()
    {
        spl_autoload_register('Autoloader::loadClass');
    }

    /**
     * Loads the given class or interface.
     *
     * @param  string    $className The name of the class
     * @return bool|null True if loaded, null otherwise
     */
    public static function loadClass($className)
    {
        // Check if class had already been loaded, and if not find it
        if (class_exists($className, false) == false) {        
            if ($file = self::findFile($className)) {
                include $file;

                return true;
            }
        }
    }

    /**
     * Finds class by it name
     * @param   string  $class  Name of the class to find
     * @return  string|null     Full name and path to the class file, otherwise false
     */
    public static function findFile($class)
    {
        // Find a path and file name
        if (false !== $pos = strrpos($class, '\\')) {
            // namespaced class name
            $classPath = strtr(substr($class, 0, $pos), '\\', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $className = substr($class, $pos + 1);
        } else {
            // PEAR-like class name
            $classPath = null;
            $className = $class;
        }

        $classPath .= strtr($className, '_', DIRECTORY_SEPARATOR) . '.php';
        $include_paths  = explode(PATH_SEPARATOR, get_include_path());

        // Iterate through all of the include paths defined,
        // and check whether the class file exists.
        foreach ( $include_paths as $path )
        {
            if (file_exists($path."/".$classPath)) {
                return $path . DIRECTORY_SEPARATOR . $classPath;
            }
        }
    }
}
