<?php

require __DIR__ . '/../../vendor/autoload.php';
$core = \AgencyBoilerplate\Handlebars\Core::init([
   'partialDir' => [__DIR__ . '/tmpl/']
]);

$data = [
   'object-1' => [
      'key' => 'value'
   ],
   'object-2' => [
      'xyz' => 'value'
   ]
];

echo $core->getEngine()->render('index', $data);

?>
