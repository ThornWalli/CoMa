<?php

namespace CoMa\Base;

use CoMa\Helper\Template;
use Handlebars\Handlebars;

class TemplateArea extends \CoMa\Base\ThemeArea
{

   const TEMPLATE_NAME = 'Theme Area';
   const TEMPLATE_ID = 'default-area';
   const TEMPLATE_PATH = 'areas/default';

   public function render($options = ['edit' => null])
   {

      $core = \AgencyBoilerplate\Handlebars\Core::getInstance();
      $options['html'] = $core->getEngine()->render($this->getTemplatePath(),
         $this->getTemplateData()
      );
      if (array_key_exists('echo', $options) && $options['echo']) {
         return parent::render($options);
      } else {
         parent::render($options);
      }

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
      $data = $this->getFlatProperties();
      if (count($this->getChildrens()) > 0) {
         $components = [];
         foreach ($this->getChildrens() as $component) {
            $components[] = $component->render(['echo' => true]);
         }
         $data['area-content'] = $components;
      } else {
         if (!array_key_exists(Template::PROPERTY_STYLE_CLASS, $data)) {
            $data[Template::PROPERTY_STYLE_CLASS] = '';
         }
         $data[Template::PROPERTY_STYLE_CLASS] .= ' coma-area-empty';
      }
      return $data;
   }

}

?>
