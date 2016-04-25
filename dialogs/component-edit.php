<?php

$properties = [];

if(\CoMa\Helper\Base::getSession('component-id')) {

    /**
     * @type CoMa\Base\ThemeComponent $component
     */
    $component= \CoMa\Helper\Component::getComponentById(\CoMa\Helper\Base::getSession('component-id'));

    $areaId = $component->getParentId();

    /**
     * @type CoMa\Base\ThemeComponent $componentClass
     */
    $componentClass = $component->getClass();
    $properties = $component->getProperties();
    $position = $component->getPosition();

} else {
    $componentClass= \CoMa\Helper\Base::getSession('component-class');
    $areaId= \CoMa\Helper\Base::getSession('area-id');
    $area= \CoMa\Helper\Component::getAreaById($areaId);
    if ($area) {
    $position = $area->getPosition();
    } else {
        $position= null;
    }
}

if ($position) {

?>
<div class="coma-controller partial"
     data-coma-controller="components/dialog/EditComponent" data-partial="coma/component/component-edit"<?php echo CoMa\Helper\Base::renderTagAttributes([
    'deep-modal' => \CoMa\DEEP_MODAL,
    'class' => $componentClass,
    'id' => CoMa\Helper\Base::getSession('component-id'),
    'area-id' => $areaId,
    'position' => $position,
    'ajax' => \CoMa\ADMIN_URL
], 'data'); ?> data-target='.partial[data-partial="coma/component/controller/area"][data-id="<?php echo $areaId; ?>"]'>

    <?php

    $component = new $componentClass();

    $propertyDialog = $component->getPropertyDialog();
    $propertyDialog->title(__('Edit Component', \CoMa\PLUGIN_NAME).' ['.$componentClass.']');
    $propertyDialog->render($properties);

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