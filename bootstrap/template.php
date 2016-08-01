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
  }

   public function controller($class, $dir = null)
   {

      if (is_null($dir)) {
         $dir = $this->basePath . '/controller/';
      }
      if (file_exists($dir)) {
         foreach (scandir($dir) as $file) {

            if (is_dir($dir . $file) && substr($file, 0, 1) !== '.')
               $this->controller($class, $dir . $file . '/');

            if (substr($file, 0, 2) !== '._' && preg_match("/.php$/i", $file)) {

               if (!file_exists($dir . preg_replace('/.+\\\\controller\\\\(.*)/', '$1.php', $class))) {
                  include_once $dir . $file;
               } else {
                  include_once $dir . preg_replace('/.+\\\\controller\\\\(.*)/', '$1.php', $class);
               }
            }
         }
      }
   }

}


?>
