<?php

$properties = [];


$properties= \CoMa\Helper\Base::getGlobalProperties();

if (true) {

    ?>
    <div class="coma-controller"
         data-coma-controller="components/dialog/GlobalProperties"<?php echo CoMa\Helper\Base::renderTagAttributes([
        'deep-modal' => \CoMa\DEEP_MODAL,
        'ajax' => \CoMa\ADMIN_URL
    ], 'data'); ?>>

        <?php

        $propertyDialog = new CoMa\Base\PropertyDialog();
        $propertyDialog->title(__('Global Properties', \CoMa\PLUGIN_NAME));
        $propertyDialog = apply_filters(\CoMa\WP\Filter\GLOBAL_PROPERTIES_DIALOG, $propertyDialog);
        $propertyDialog->render($properties->get());

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