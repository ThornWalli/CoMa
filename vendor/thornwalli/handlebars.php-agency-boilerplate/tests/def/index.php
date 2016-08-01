<?php

require __DIR__ . '/../../vendor/autoload.php';
$core = \AgencyBoilerplate\Handlebars\Core::init([
   'partialDir' => [__DIR__ . '/tmpl/']
]);

$yamlData = $core->getDefaultPartialData('index');
echo $core->getEngine()->render('index', $yamlData);

?>

<h2>Property Definitions Data</h2>

<table border="1">

   <tbody>

   <tr>

      <th>Group</th>
      <th>Name</th>
      <th>Type</th>
      <th>Title</th>
      <th>Description</th>
      <th>default</th>

   </tr>

   <?php

   $definitions = $GLOBALS[$core::getInstance()->getGlobalDefTemp()];
   foreach ($GLOBALS[$core::getInstance()->getGlobalDefTemp()] as $groupName => $group) {
      foreach ($group as $field) {
         ?>
         <tr>
            <td><?php echo $groupName; ?></td>
            <td><?php echo array_key_exists('name', $field) ? $field['name'] : ''; ?></td>
            <td><?php echo array_key_exists('type', $field) ? $field['type'] : ''; ?></td>
            <td><?php echo array_key_exists('title', $field) ? $field['title'] : ''; ?></td>
            <td><?php echo array_key_exists('desc', $field) ? $field['desc'] : ''; ?></td>
            <td><?php echo array_key_exists('default', $field) ? $field['default'] : ''; ?></td>
         </tr>
         <?php
      }
   }

   ?>


   </tbody>

</table>
