<?php

namespace CoMa\Base;

use CoMa\Helper\Base;

class Page extends Controller
{
   const TEMPLATE_NAME = 'Area Component';
   const TEMPLATE_ID = 'default-area';
   const TEMPLATE_PATH = 'area';

   public function render()
   {

      $includePath = $this->getTemplatePath() . '.php';
      global $CONTENT_MANAGER_PARENT_COMPONENT;
      $tmpParent = $CONTENT_MANAGER_PARENT_COMPONENT;

      $CONTENT_MANAGER_PARENT_COMPONENT = $this;

      if (Base::isEditMode()) {
         include(\CoMa\PLUGIN_TEMPLATE_PATH . 'area.php');
      } else {
         include($includePath);
      }

      $CONTENT_MANAGER_PARENT_COMPONENT = $tmpParent;
      $includePath = null;

   }

   public function getTemplatePath()
   {
      return \CoMa\THEME_TEMPLATE_PATH . '/area' . self::TEMPLATE_PATH;
   }

   /**
    * Ruft alle Componten ab, die in der Area verwendet werden können
    * @return array
    */
   public static function getClasses()
   {
      return [];
   }


}

?>
