<?php

require __DIR__ . '/../../vendor/autoload.php';
$core = \AgencyBoilerplate\Handlebars\Core::init([
   'partialDir' => [__DIR__ . '/tmpl/']
]);

$data = [
   'object-1' => [
      'hello' => 'world'
   ],
   'object-2' => null
];

echo $core->getEngine()->render('index', $data);

?>
