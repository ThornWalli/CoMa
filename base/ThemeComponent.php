<?php

namespace CoMa\Base;

use CoMa\Helper\Base;

class ThemeComponent extends Component
{
   const TEMPLATE_NAME = 'Theme Component';
   const TEMPLATE_ID = 'default-component';
   const TEMPLATE_PATH = 'component.default';

   public function __construct($properties = [], $id = null)
   {
      parent::__construct($properties, $id);
      $this->setControls([]);
   }

   public function getPropertyDialog()
   {
      return self::defaultPropertyDialog();
   }

   /**
    * @param string $position
    * @param mixed $class
    * @return string
    */
   public function getArea($position, $class)
   {
      return Base::getArea($position, $class, $this->getId());
   }

}

?>
