<?php

namespace CoMa;

class PluginBoostrap
{

    private $basePath = '';
    public static $loader;

    public static function init($basePath = '')
    {
        if (self::$loader == NULL)
            self::$loader = new self($basePath);

        return self::$loader;
    }

    public function __construct($basePath = '')
    {
        $this->basePath = $basePath;
        spl_autoload_register([$this, 'base']);
        spl_autoload_register([$this, 'helper']);
    }


    public function base($class, $path = null)
    {
        if (class_exists($class)) {
            return;
        }

        if (strpos($class, 'CoMa\Base') > -1) {
            if (is_null($path)) {
                $path = $this->basePath . '/base';
            }
            $classes = [
                'CoMa\Base\Area' => 'Area.php',
                'CoMa\Base\Component' => 'Component.php',
                'CoMa\Base\Controller' => 'Controller.php',
                'CoMa\Base\GlobalProperties' => 'GlobalProperties.php',
                'CoMa\Base\Page' => 'Page.php',
                'CoMa\Base\PageProperties' => 'PageProperties.php',
                'CoMa\Base\PropertyHandler' => 'PropertyHandler.php',
                'CoMa\Base\ThemeArea' => 'ThemeArea.php',
                'CoMa\Base\ThemeComponent' => 'ThemeComponent.php',
                'CoMa\Base\PropertyDialog' => 'PropertyDialog.php',
                'CoMa\Base\PropertyDialog\Tab' => 'propertyDialog/Tab.php',
                'CoMa\Base\PropertyDialog\Field' => 'propertyDialog/Field.php',
            ];
            if (array_key_exists($class, $classes)) {
                $filePath = $path . '/' . $classes[$class];
                include($filePath);
            } else {
                $split = explode('\\', $class);
                array_pop($split);
                $this->base(implode('\\', $split));
            }

        }
    }

    public function helper($class, $path = null)
    {
        if (strpos($class, 'CoMa\Helper') > -1) {
            if (is_null($path)) {
                $path = $this->basePath . '/helper';
            }
            $classes = [
                'CoMa\Helper\Base' => 'Base.php',
                'CoMa\Helper\Cache' => 'Cache.php',
                'CoMa\Helper\Component' => 'Component.php',
                'CoMa\Helper\Controller' => 'Controller.php',
                'CoMa\Helper\Install' => 'Install.php',
                'CoMa\Helper\InstallEditor' => 'InstallEditor.php',
                'CoMa\Helper\InstallOptions' => 'InstallOptions.php',
                'CoMa\Helper\Page' => 'Page.php',
                'CoMa\Helper\Property' => 'Property.php',
                'CoMa\Helper\Revision' => 'Revision.php',
                'CoMa\Helper\Debug' => 'Debug.php'
            ];
            if (array_key_exists($class, $classes)) {
                $filePath = $path . '/' . $classes[$class];
                include($filePath);
            } else {
                $split = explode('\\', $class);
                array_pop($split);
                $this->base(implode('\\', $split));
            }
        }
    }
}


?>