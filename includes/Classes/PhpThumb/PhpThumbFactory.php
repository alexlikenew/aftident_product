<?php


namespace Classes\PhpThumb;

use Classes\PhpThumb\GdThumb as GdThumb;


class PhpThumbFactory
{
    /**
     * Which implemenation of the class should be used by default
     *
     * Currently, valid options are:
     *  - imagick
     *  - gd
     *
     * These are defined in the implementation map variable, inside the create function
     *
     * @var string
     */
    public static $defaultImplemenation = 'gd';//DEFAULT_THUMBLIB_IMPLEMENTATION;
    /**
     * Where the plugins can be loaded from
     *
     * Note, it's important that this path is properly defined.  It is very likely that you'll
     * have to change this, as the assumption here is based on a relative path.
     *
     * @var string
     */
    public static $pluginPath = __DIR__;//THUMBLIB_PLUGIN_PATH;

    /**
     * Factory Function
     *
     * This function returns the correct thumbnail object, augmented with any appropriate plugins.
     * It does so by doing the following:
     *  - Getting an instance of PhpThumb
     *  - Loading plugins
     *  - Validating the default implemenation
     *  - Returning the desired default implementation if possible
     *  - Returning the GD implemenation if the default isn't available
     *  - Throwing an exception if no required libraries are present
     *
     * @return GdThumb
     * @uses PhpThumb
     * @param string $filename The path and file to load [optional]
     */
    public static function create ($filename = null, $options = array(), $isDataStream = false)
    {
        // map our implementation to their class names
        $implementationMap = array
        (
            'imagick'	=> 'ImagickThumb',
            'gd' 		=> 'GdThumb'
        );

        // grab an instance of PhpThumb
        $pt = PhpThumb::getInstance();
        // load the plugins
        $pt->loadPlugins(self::$pluginPath);

        $toReturn = null;

        // attempt to load the default implementation
       if ($pt->isValidImplementation('gd'))
        {
            $imp = $implementationMap['gd'];
            $implementation = 'gd';
            if($imp == 'GdThumb')
                $toReturn = new GdThumb($filename, $options, $isDataStream);
            else
                $toReturn = new $imp($filename, $options, $isDataStream);
        }
        // throw an exception if we can't load
        else
        {
            throw new Exception('You must have either the GD or iMagick extension loaded to use this library');
        }

        $registry = $pt->getPluginRegistry($implementation);
        $toReturn->importPlugins($registry);
        return $toReturn;
    }
}