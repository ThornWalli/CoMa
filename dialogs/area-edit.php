<?php

$properties = [];

if(\CoMa\Helper\Base::getSession('area-id')) {

    /**
     * @type CoMa\Base\ThemeComponent $component
     */
    $area= \CoMa\Helper\Component::getComponentById(\CoMa\Helper\Base::getSession('area-id'));

    $areaId = $area->getParentId();

    /**
     * @type CoMa\Base\ThemeArea $areaClass
     */
    $areaClass = $area->getClass();
    $properties = $area->getProperties();
    $position = $area->getPosition();

    $id= \CoMa\Helper\Base::getSession('area-id');

    ?>
    <div class="coma-controller partial"
         data-coma-controller="components/dialog/EditArea"
         data-partial="coma/component/component-edit"<?php echo CoMa\Helper\Base::renderTagAttributes([
        'deep-modal' => \CoMa\DEEP_MODAL,
        'class' => $componentClass,
        'id' => $id,
        'ajax' => \CoMa\ADMIN_URL
    ], 'data'); ?>
         data-target='.partial[data-partial="coma/component/controller/area"][data-id="<?php echo $id; ?>"]'>

        <?php

        /**
         * @type \CoMa\Base\ThemeArea $area
         */
        $area = new $areaClass();

        $propertyDialog = $area->getPropertyDialog();
        $propertyDialog->title(__('Edit Area', \CoMa\PLUGIN_NAME) . ' [' . $areaClass . ']');
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