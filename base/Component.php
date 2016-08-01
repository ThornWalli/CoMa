<?php

namespace CoMa\Base;

class Component extends Controller
{
   const TYPE = \CoMa\Helper\Base::TYPE_COMPONENT;

   const TEMPLATE_NAME = 'Component';
   const TEMPLATE_ID = 'default-component';
   const TEMPLATE_PATH = 'component';

   public function __construct($properties = [], $id = null)
   {
      parent::__construct($properties, $id);
      $this->setControls([]);
   }

   /**
    * @param array $options
    * @return string
    */
   public function render($options = ['edit' => null, 'path' => null, 'html' => null, 'echo' => false])
   {


      if (!array_key_exists('path', $options) || !$options['path']) {
         $options['path'] = $this->getTemplatePath() . '.php';
      }
      if (!array_key_exists('echo', $options) ) {
         $options['echo'] = false;
      }
      if (!array_key_exists('edit', $options) ) {
         $options['edit'] = false;
      }

      global $CONTENT_MANAGER_PARENT_COMPONENT;
      $tmpParent = $CONTENT_MANAGER_PARENT_COMPONENT;


      
      if ($options['echo']) {
         ob_start();
      }
      $CONTENT_MANAGER_PARENT_COMPONENT = $this;
      
      do_action(\CoMa\WP\Action\BEFORE_RENDER, $this);

      if (\CoMa\Helper\Base::isEditMode() && $options['edit'] == null || $options['edit']) {
         include(\CoMa\PLUGIN_TEMPLATE_PATH . 'component.php');
      } else {
         if (array_key_exists('html', $options) && $options['html']) {
            echo $options['html'];
         } else {
            include($options['path']);
         }
      }

      do_action(\CoMa\WP\Action\AFTER_RENDER, $this);
      
      $CONTENT_MANAGER_PARENT_COMPONENT = $tmpParent;

      if ($options['echo']) {
         return ob_get_clean();
      }


   }

   public function getTemplatePath()
   {

      return \CoMa\THEME_TEMPLATE_PATH . '/component/' . $this::TEMPLATE_PATH;

   }

}

?>
