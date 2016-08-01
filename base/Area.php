<?php

namespace CoMa\Base;

use CoMa\Helper\Base;

class Area extends Controller
{
   const TYPE = Base::TYPE_AREA;

   const TEMPLATE_NAME = 'Area Component';
   const TEMPLATE_ID = 'default-area';
   const TEMPLATE_PATH = 'area';

   const COMPONENT_ALIGNMENT = 'vertical';

   public function __construct($properties = [], $id = null)
   {
      parent::__construct($properties, $id);
      $this->setControls([]);
   }

   public function render($options = ['edit' => null, 'path' => null, 'html' => null])
   {
      if (!array_key_exists('path', $options) || !$options['path']) {
         $options['path'] = $this->getTemplatePath() . '.php';
      }
      if (!array_key_exists('echo', $options)) {
         $options['echo'] = false;
      }
      if (!array_key_exists('edit', $options)) {
         $options['edit'] = false;
      }
      if (!array_key_exists('path', $options)) {
         $options['path'] = null;
      }

      $this->setControls([]);

      if ($this->getDisabled() && !Base::isEditMode()) {
         return;
      }

      if ($options['echo']) {
         ob_start();
      }

      global $CONTENT_MANAGER_PARENT_COMPONENT;
      $tmpParent = $CONTENT_MANAGER_PARENT_COMPONENT;

      do_action(\CoMa\WP\Action\BEFORE_RENDER, $this);

      $CONTENT_MANAGER_PARENT_COMPONENT = $this;
      if (\CoMa\Helper\Base::isEditMode() && $options['edit'] == null || $options['edit']) {
         include(\CoMa\PLUGIN_TEMPLATE_PATH . 'area.php');
      } else {
         if (array_key_exists('html', $options) && $options['html']) {
            echo $options['html'];
         } else {
            include($options['path']);
         }
      }

      do_action(\CoMa\WP\Action\AFTER_RENDER, $this);

      $CONTENT_MANAGER_PARENT_COMPONENT = $tmpParent;

      $area = null;
      if ($options['echo']) {
         $area = ob_get_clean();
      }
      $includePath = null;
      return $area;
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
