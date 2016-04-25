<?php

if (\CoMa\Helper\Base::isEditMode() && !is_admin() || get_current_screen()->parent_base == 'coma') {

    ?>

    <script type="text/html" id="coma-template-log">

        <div class="{{= type }} notice is-dismissible">
            <p>{{= text }}</p>
        </div>

    </script>

    <div class="coma-controller partial" data-coma-controller="components/Logs" data-partial="wp-content-manage/logs">

        <div>

            <?php

            foreach (\CoMa\Helper\Base::getLogs() as $log) {

                CoMa\Helper\Base::renderAdminNotice($log['text'], $log['type']);

            }

            ?>

        </div>

    </div>

    <?php

    echo '<div class="coma-controller" data-coma-controller="CoMa"' . CoMa\Helper\Base::renderTagAttributes(['deep-modal' => 'coma-dialog', 'page-id' => CoMa\Helper\Base::getSession('page-id'), 'ajax' => \CoMa\ADMIN_URL], 'data') . '></div>';
    if (\CoMa\Helper\Base::isEditMode()) {
        include(\CoMa\PLUGIN_TEMPLATE_PATH . 'modal.php');
    }


}

?>