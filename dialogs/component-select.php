<?php

/**
 * @type \CoMaTheme\Area\Area $area
 */
$area = \CoMa\Helper\Component::getAreaById(\CoMa\Helper\Base::getSession('area-id'));

if ($area) {

  ?>
  <div class="coma-controller partial"
       data-coma-controller="components/dialog/SelectComponent"
       data-partial="coma/component/component-select"<?php echo CoMa\Helper\Base::renderTagAttributes([
    'deep-modal' => \CoMa\DEEP_MODAL,
    'deep-component' => \CoMa\DEEP_COMPONENT,
    'ajax' => \CoMa\ADMIN_URL,
    'area-id' => $area->getId()
  ], 'data'); ?>>

    <?php

    $propertyDialog = new CoMa\Base\PropertyDialog();
    $propertyDialog->title(__('Component Select', \CoMa\PLUGIN_NAME));
    $tab = $propertyDialog->getTab();

    foreach ($area->getClasses() as $class) {
      $tab->addRadioBox('component', $class::TEMPLATE_NAME)->defaultValue($class)->cssClass('radio-component');
    }
    $propertyDialog->render();

    ?>

  </div>

  <?php

} else {

  ?>

  <h2><?php echo __('Error', \CoMa\PLUGIN_NAME); ?></h2>
  <div class="content">

    <div class="partial error" data-partial="coma/assetboard/message">
      <p><?php echo __('Here, something went wrong ...', \CoMa\PLUGIN_NAME); ?></p>
    </div>

  </div>


  <?php

}

?>
