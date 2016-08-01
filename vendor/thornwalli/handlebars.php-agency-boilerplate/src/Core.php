<?php

namespace AgencyBoilerplate\Handlebars;


class Core
{

   const DEF_DEFAULT_GROUP = 'content';
   protected static $instance = null;
   protected static $options = null;


   /**
    * @var \Handlebars\Handlebars
    */
   protected $engine;
   protected $globalDefTemp;
   protected $globalMixinPath;
   protected $globalMixinDeactivated;
   private $partialsDefaultData = [];
   protected $defDefaultGroup;

   /**
    * @return \AgencyBoilerplate\Handlebars\Core
    */
   public static function getInstance()
   {
      self::hasInstance();
      return self::$instance;
   }

   public function __construct($engine, $globalDefTemp, $globalMixinPath, $globalMixinDeactivated, $defaultDefGroup)
   {
      $this->engine = $engine;
      $this->globalDefTemp = $globalDefTemp;
      $GLOBALS[$globalDefTemp] = null;
      $this->globalMixinDeactivated = $globalMixinDeactivated;
      $GLOBALS[$globalMixinDeactivated] = null;
      $this->globalMixinPath = $globalMixinPath;
      $GLOBALS[$globalMixinPath] = [];
      $this->defDefaultGroup = $defaultDefGroup;
   }

   public static function init($options)
   {
      if (is_array($options)) {
         if (!array_key_exists('partialDir', $options)) {
            $options['partialDir'] = [];
         }
         if (!array_key_exists('extension', $options)) {
            $options['extension'] = '.hbs';
         }
         if (!array_key_exists('prefix', $options)) {
            $options['prefix'] = '';
         }
         if (!array_key_exists('globalDefTemp', $options)) {
            $options['globalDefTemp'] = 'AGENCY_BOILERPLATE_HBS_DEF_TEMP';
         }
         if (!array_key_exists('globalMixinPath', $options)) {
            $options['globalMixinPath'] = 'AGENCY_BOILERPLATE_HBS_MIXIN_PATH';
         }
         if (!array_key_exists('globalMixinDeactivated', $options)) {
            $options['globalMixinDeactivated'] = 'AGENCY_BOILERPLATE_HBS_MIXIN_DEACTIVATED';
         }
         if (!array_key_exists('defDefaultGroup', $options)) {
            $options['defDefaultGroup'] = self::DEF_DEFAULT_GROUP;
         }
      } else {
         throw new \InvalidArgumentException(
            'empty options'
         );
      }

      self::$options = $options;
      $baseDirs = $options['partialDir'];
      self::$instance = new self(new \Handlebars\Handlebars(array(
         'helpers' => new Helpers(),
         'loader' => new \AgencyBoilerplate\Handlebars\Loader\FilesystemLoader($baseDirs, $options),
         'partials_loader' => new \AgencyBoilerplate\Handlebars\Loader\FilesystemLoader($baseDirs, $options)
      )), $options['globalDefTemp'], $options['globalMixinPath'], $options['globalMixinDeactivated'], $options['defDefaultGroup']);
      return self::$instance;
   }

   public static function hasInstance()
   {
      if (!self::$instance) {
         throw new \InvalidArgumentException(
            'init core and get instance from core.'
         );
      }
   }

   /**
    * @return \Handlebars\Handlebars
    */
   public function getEngine()
   {
      self::hasInstance();
      return $this->engine;
   }

   public function getGlobalDefTemp()
   {
      return $this->globalDefTemp;
   }

   public function getGlobalMixinPath()
   {
      return $this->globalMixinPath;
   }

   public function getGlobalMixinDeactivated()
   {
      return $this->globalMixinDeactivated;
   }
   public function getDefDefaultGroup()
   {
      return $this->defDefaultGroup;
   }

   public function getDefData($partialName, $properties = null)
   {
      $core = \AgencyBoilerplate\Handlebars\Core::getInstance();
      $fileContent = $core->getEngine()->getPartialsLoader()->load($partialName);


      $GLOBALS[\AgencyBoilerplate\Handlebars\Core::getInstance()->getGlobalDefTemp()] = [];
      $GLOBALS[\AgencyBoilerplate\Handlebars\Core::getInstance()->getGlobalMixinDeactivated()] = true;
      $core->getEngine()->render($partialName, $core::getDefaultPartialData($partialName));
      $GLOBALS[\AgencyBoilerplate\Handlebars\Core::getInstance()->getGlobalMixinDeactivated()] = false;

      $key = explode('/', $partialName);
      $key = $key[count($key) - 1];
      $properties[$key] = $GLOBALS[$core->getGlobalDefTemp()];
      return $properties;


   }


   public function getPathsFromMixins($partialName)
   {
      $core = \AgencyBoilerplate\Handlebars\Core::getInstance();
      $fileContent = $core->getEngine()->getPartialsLoader()->load($partialName);
      preg_match_all("/{{[{#]mixin \\\"([^\"]*)\\\"[^{}]*}}/", $fileContent, $matches);
      return $matches[1];
   }

   public function getDefaultPartialData($partialPath)
   {
      if (array_key_exists($partialPath, $this->partialsDefaultData)) {
         return $this->partialsDefaultData[$partialPath];
      } else {
         $this->getEngine()->getPartialsLoader()->load($partialPath);
         return $this->partialsDefaultData[$partialPath];
      }
   }

   public function registerDefaultPartialData($partialPath, $data)
   {
      $this->partialsDefaultData[$partialPath] = $data;
   }

}

?>
