<?php

namespace CoMa\Base;

class ThemeArea extends Area
{
   const TEMPLATE_NAME = 'Theme Area';
   const TEMPLATE_ID = 'default-area';
   const TEMPLATE_PATH = 'area';

   public function __construct($properties = [], $id = null)
   {
      parent::__construct($properties, $id);
      $this->setControls([]);
   }

   public function getTemplatePath()
   {
      return \CoMa\THEME_TEMPLATE_PATH . '/area/' . $this::TEMPLATE_PATH;
   }

}

?>
