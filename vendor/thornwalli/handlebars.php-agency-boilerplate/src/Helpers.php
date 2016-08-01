<?php

namespace AgencyBoilerplate\Handlebars;

class Helpers extends \Handlebars\Helpers
{

   protected function addDefaultHelpers()
   {
      parent::addDefaultHelpers();

      $storage = new \JustBlackBird\HandlebarsHelpers\Layout\BlockStorage();
      $this->add('block', new \JustBlackBird\HandlebarsHelpers\Layout\BlockHelper($storage));
      $this->add('mixin', new \AgencyBoilerplate\Handlebars\Helpers\MixinHelper($storage));
      $this->add('with', new \AgencyBoilerplate\Handlebars\Helpers\WithHelper());
      $this->add('content', new \JustBlackBird\HandlebarsHelpers\Layout\OverrideHelper($storage));
      $this->add('lookup', new \AgencyBoilerplate\Handlebars\Helpers\LookupHelper());
      $this->add('stringify', new \AgencyBoilerplate\Handlebars\Helpers\StringifyHelper());
      $this->add('def', new \AgencyBoilerplate\Handlebars\Helpers\DefHelper());
   }

}

?>
