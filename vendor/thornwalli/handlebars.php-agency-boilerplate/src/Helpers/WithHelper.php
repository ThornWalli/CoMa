<?php
namespace AgencyBoilerplate\Handlebars\Helpers;

class WithHelper implements \Handlebars\Helper
{
   /**
    * @param \Handlebars\Template $template
    * @param \Handlebars\Context $context
    * @param array $args
    * @param string $source
    * @return mixed
    */
   public function execute(\Handlebars\Template $template, \Handlebars\Context $context, $args, $source)
   {
      $buffer = null;
      if ($context->get($args)) {
         $context->with($args);
         $buffer = $template->render($context);
         $context->pop();
      }
      return $buffer;
   }
}

?>
