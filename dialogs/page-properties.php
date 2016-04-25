<?php


$id= \CoMa\Helper\Base::POST('page-id');
$properties= \CoMa\Helper\Base::getPageProperties($id)->get();

if ($id) {

    ?>
    <div class="coma-controller"
         data-coma-controller="components/dialog/PageProperties"<?php echo CoMa\Helper\Base::renderTagAttributes([
        'deep-modal' => \CoMa\DEEP_MODAL,
        'page-id' => $id,
        'id' => $id,
        'ajax' => \CoMa\ADMIN_URL
    ], 'data'); ?>>

        <?php

        $propertyDialog = new CoMa\Base\PropertyDialog();
        $propertyDialog = apply_filters(\CoMa\WP\Filter\PAGE_PROPERTIES_DIALOG, $propertyDialog, $id);
        /**
         * @type CoMa\Base\PropertyDialog $propertyDialog
         */
        $propertyDialog->title(__('Edit Page', \CoMa\PLUGIN_NAME) . ' [' . get_the_title($id) . ']');
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