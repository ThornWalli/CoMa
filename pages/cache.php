<div class="wrap">
    <h2><?php echo __(\CoMa\PLUGIN_TITLE_SHORT, \CoMa\PLUGIN_NAME) . ' - ' . __('Cache', \CoMa\PLUGIN_NAME); ?></h2>
</div>

<?php

if(\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\CACHE_CLEAR_ATTACHMENTS)) {

    ?>

    <div>

        <div class="coma-controller partial" data-coma-controller="components/admin/CacheRefresh"
             data-partial="coma/component/cache-refresh"<?php echo CoMa\Helper\Base::renderTagAttributes([
            'ajax' => \CoMa\ADMIN_URL
        ], 'data'); ?>>

            <div class="buttons">

                <input type="button" class="button button-primary" name="refreshThumbnails"
                       value="<?php echo __('Refresh Thumbnails', \CoMa\PLUGIN_NAME); ?>">

            </div>
            <div class="progress">
                <div data-progress="0"></div>
            </div>
            <ul class="results"></ul>

        </div>

    </div>

    <?php

}

?>

