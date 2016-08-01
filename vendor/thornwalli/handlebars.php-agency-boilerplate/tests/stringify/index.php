<?php

require __DIR__ . '/../../vendor/autoload.php';
$core = \AgencyBoilerplate\Handlebars\Core::init([
   'partialDir' => [__DIR__ . '/tmpl/']
]);

$data = [
   'object' => [
      'hello' => 'world'
   ],
   'array' => [
      0, 1, 2, 3
   ]
];

echo $core->getEngine()->render('index', $data);

?>
