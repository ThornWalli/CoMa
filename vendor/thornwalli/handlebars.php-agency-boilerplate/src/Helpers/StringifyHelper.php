<?php
namespace AgencyBoilerplate\Handlebars\Helpers;

class StringifyHelper implements \Handlebars\Helper
{
   /**
    * Override "with" helper for remove buffer output, when arguments empty.
    * @param \Handlebars\Template $template
    * @param \Handlebars\Context $context
    * @param array $args
    * @param string $source
    * @return mixed
    */
   public function execute(\Handlebars\Template $template, \Handlebars\Context $context, $args, $source)
   {
      $parsedArgs = $template->parseArguments($args);
      return json_encode($context->get(current($parsedArgs)));
   }
}

?>
