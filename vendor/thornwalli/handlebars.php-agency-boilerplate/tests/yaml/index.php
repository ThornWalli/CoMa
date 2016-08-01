<?php

require __DIR__ . '/../../vendor/autoload.php';
$core = \AgencyBoilerplate\Handlebars\Core::init([
   'partialDir' => [__DIR__ . '/tmpl/']
]);

$yamlData = $core->getDefaultPartialData('index');
echo $core->getEngine()->render('index', $yamlData);

?>

<h2>Yaml Data</h2>

<?php
echo '<pre>';
print_r($yamlData);
echo '</pre>';
?>
