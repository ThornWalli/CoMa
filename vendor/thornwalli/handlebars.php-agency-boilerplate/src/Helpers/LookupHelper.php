<?php
namespace AgencyBoilerplate\Handlebars\Helpers;

class LookupHelper implements \Handlebars\Helper
{
   public function execute(\Handlebars\Template $template, \Handlebars\Context $context, $args, $source)
   {
      $parsedArgs = $template->parseArguments($args);
      $data = $context->get($parsedArgs[0]);
      if ($data) {
         $value = $context->get($parsedArgs[1]);
         if ($value && array_key_exists($value, $data)) {
            return $data[$value];
         }
      }
   }
}

?>
