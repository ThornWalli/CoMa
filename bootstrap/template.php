<?php

namespace CoMa;

class TemplateBoostrap
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

        spl_autoload_register([$this, 'controller']);
        spl_autoload_register([$this, 'area']);
        spl_autoload_register([$this, 'component']);
        spl_autoload_register([$this, 'templates']);
    }

    public function controller($class)
    {
        $class = preg_replace('/_controller$/ui', '', $class);
        set_include_path($this->basePath . '/controller/');
        spl_autoload_extensions('.php');
        spl_autoload($class);
    }

    public function area($class, $dir = null)
    {
        if (is_null($dir)) {
            $dir = $this->basePath . '/area/';
        }
        if (file_exists($dir)) {
            foreach (scandir($dir) as $file) {

                if (is_dir($dir . $file) && substr($file, 0, 1) !== '.')
                    $this->area($class, $dir . $file . '/');

                if (substr($file, 0, 2) !== '._' && preg_match("/.php$/i", $file)) {

                    if (!file_exists($dir . preg_replace('/.+\\\\Area\\\\(.*)/', '$1.php', $class))) {
                        include_once $dir . $file;
                    } else {
                        include_once $dir . preg_replace('/.+\\\\Area\\\\(.*)/', '$1.php', $class);
                    }
                }
            }
        }
    }

    public function component($class, $dir = null)
    {

        if (is_null($dir)) {
            $dir = $this->basePath . '/component/';
        }
        if (file_exists($dir)) {
            foreach (scandir($dir) as $file) {

                if (is_dir($dir . $file) && substr($file, 0, 1) !== '.')
                    $this->component($class, $dir . $file . '/');

                if (substr($file, 0, 2) !== '._' && preg_match("/.php$/i", $file)) {

                    if (!file_exists($dir . preg_replace('/.+\\\\Component\\\\(.*)/', '$1.php', $class))) {
                        include_once $dir . $file;
                    } else {
                        include_once $dir . preg_replace('/.+\\\\Component\\\\(.*)/', '$1.php', $class);
                    }
                }
            }
        }
    }


    public function templates($class)
    {
        $class = preg_replace('/_templates$/ui', '', $class);

        set_include_path($this->basePath . PATH_SEPARATOR . '/templates/');
        spl_autoload_extensions('.templates.php');
        spl_autoload($class);
    }

}


?>