<?php

namespace AgencyBoilerplate\Handlebars\Loader;

use AgencyBoilerplate\Handlebars\Core;

class FilesystemLoader extends \Handlebars\Loader\FilesystemLoader
{

   /**
    * Remove yaml from template content.
    * @param string $name template name
    * @return mixed|string
    */
   protected function loadFile($name)
   {
      // if package path, reference to index file.
      if (preg_match("/.*-pkg-.*/", $name, $match) && !preg_match("/\\/index$/", $name, $match)) {
         $name = $name . '/index';
      }
      $fileContent = parent::loadFile($name);
      if (preg_match("/^---[.\\s\\S]*---/", $fileContent, $match)) {
         Core::getInstance()->registerDefaultPartialData($name, \Spyc::YAMLLoad($match[0]));
      }
      // replace yaml
      return preg_replace("/^(---[.\\s\\S]*---)/", "", $fileContent);
   }

}

?>
