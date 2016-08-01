<?php

namespace CoMa\Base;

use AgencyBoilerplate\Handlebars\Core;
use CoMa\Helper\Base;
use CoMa\Helper\Template;

class TemplateComponent extends \CoMa\Base\ThemeComponent
{

   const TEMPLATE_NAME = 'Theme Component';
   const TEMPLATE_ID = 'default-component';
   const TEMPLATE_PATH = 'component.default';


   public function render($options = ['edit' => null])
   {

      $core = Core::getInstance();

      $options['html'] = $core->getEngine()->render($this->getTemplatePath(),
         $this->getTemplateData()
      );

      return parent::render($options);

   }

   /**
    * @return \CoMa\Base\PropertyDialog
    */
   public function getPropertyDialog()
   {
      return Template::generatePropertyDialogFromTemplate($this);
   }

   public function getTemplatePath()
   {
      return $this::TEMPLATE_PATH;
   }

   /**
    * Override this function for return data for template compile.
    * @override
    */
   public function getTemplateData()
   {
      return $this->getFlatPropertiesExtended();
   }

}

?>
