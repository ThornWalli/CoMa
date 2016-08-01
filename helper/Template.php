<?php

namespace CoMa\Helper;


use CoMa\Base\PropertyDialog\Field;
use Handlebars\Handlebars;

class Template
{

   const PROPERTY_STYLE_CLASS = 'class';
   const TAB_DEFAULT = 'default';
   const TAB_SETTINGS = 'settings';
   const TAB_CONTENT = 'content';
   protected static $themeDomain;


   protected static $registeredCustomFields = [];

   public static function registerFieldType($name, $class)
   {
      self::$registeredCustomFields[$name] = $class;
   }

   public static function getRegisteredCustomField($name)
   {
      return self::$registeredCustomFields[$name];
   }

   private static function registerDefaultFieldTypes()
   {

      self::registerFieldType('MediaSelect', \CoMa\Base\PropertyDialog\Field\MediaSelect::class);
      self::registerFieldType('MediaImageSelect', \CoMa\Base\PropertyDialog\Field\MediaImageSelect::class);
      self::registerFieldType('CheckBox', \CoMa\Base\PropertyDialog\Field\CheckBox::class);
      self::registerFieldType('TextField', \CoMa\Base\PropertyDialog\Field\TextField::class);
      self::registerFieldType('Editor', \CoMa\Base\PropertyDialog\Field\Editor::class);
      self::registerFieldType('CodeEditor', \CoMa\Base\PropertyDialog\Field\CodeEditor::class);
      self::registerFieldType('DateSelect', \CoMa\Base\PropertyDialog\Field\DateSelect::class);
      self::registerFieldType('Link', \CoMa\Base\PropertyDialog\Field\Link::class);

   }


   public static function __($text)
   {
      $newText = __($text, self::$themeDomain);
      if ($newText == $text) {
         $newText = __($text, \CoMa\PLUGIN_NAME);
      }
      return $newText;
   }

   /**
    * @param \CoMa\Base\Controller $controller
    * @param \CoMa\Base\PropertyDialog $propertyDialog
    * @return \CoMa\Base\PropertyDialog
    */
   public static function generatePropertyDialogFromTemplate($controller, $propertyDialog = null)
   {

      if (!$propertyDialog) {
         $propertyDialog = $controller::defaultPropertyDialog();
      }
      $templatePath = explode('/', $controller::TEMPLATE_PATH);
      $key = array_pop($templatePath);
      $core = \AgencyBoilerplate\Handlebars\Core::getInstance();
      foreach ($core->getDefData($controller->getTemplatePath())[$key] as $partialName => $data) {

         if (count($data) > 0) {

            if ($key == $partialName) {
               $tab = $propertyDialog->addTab(self::TAB_CONTENT, self::__('tab_' . self::TAB_CONTENT, \CoMa\PLUGIN_NAME));
               if (!$tab) {
                  $tab = $propertyDialog->addTab(self::TAB_CONTENT, self::__('tab_' . self::TAB_CONTENT, \CoMa\PLUGIN_NAME));
               }
            } else {
               $tab = $propertyDialog->addTab(str_replace('/', '_', $partialName), self::__('tab_' . str_replace('/', '_', $partialName)));
            }

            $fields = [];
            foreach ($data as $var) {
               $name = $var['name']->getString();
               $title = $name;
               if (array_key_exists('title', $var)) {
                  $title = $var['title'];
               }
               if (array_key_exists('type', $var)) {
                  $fieldClass = self::$registeredCustomFields[$var['type']->getString()];
                  /**
                   * @type Field $field ;
                   */
                  $field = new $fieldClass($name, self::__($title));
                  if (array_key_exists('desc', $var)) {
                     $field->description($var['desc']);
                  }
                  if (array_key_exists('default', $var)) {
                     $field->defaultValue($var['default']);
                  }
                  array_push($fields, $field);
               }
            }

            $tab->addFields($fields);

         }

      }
      return $propertyDialog;
   }

   public static function setup($options)
   {
      self::registerDefaultFieldTypes();

      $globalDefTemp = 'COMA_DEF_TEMP';
      if (array_key_exists('globalDefTemp', $options)) {
         $globalDefTemp = $options['globalDefTemp'];
      }
      require __DIR__ . '/../vendor_/autoload.php';
      \AgencyBoilerplate\Handlebars\Core::init([
         'globalDefTemp' => $globalDefTemp,
         'defDefaultGroup' => $options['defDefaultGroup'],
         'partialDir' => $options['partialDir']
      ]);

      self::$themeDomain = $options['themeDomain'];

   }


}

?>
