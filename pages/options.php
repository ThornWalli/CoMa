<?php


if (isset($_POST['deleteControllers'])) {

    if (isset($_POST['allowDeleteControllers']) && $_POST['allowDeleteControllers']) {
        CoMa\Helper\Controller::deleteAllControllers();
        CoMa\Helper\Base::renderAdminNotice(__('Controllers have been deleted.', \CoMa\PLUGIN_NAME), \CoMa\WP_ADMIN_NOTICE_TYPE_SUCCESS);
    } else {
        CoMa\Helper\Base::renderAdminNotice(__('Deleting need to be accepted.', \CoMa\PLUGIN_NAME), \CoMa\WP_ADMIN_NOTICE_TYPE_ERROR);
    }

} else if (isset($_POST['deletePagePost'])) {

    if (isset($_POST['allowDeletePagesPostsCache']) && $_POST['allowDeletePagesPostsCache']) {
        CoMa\Helper\Cache::deleteCache('page');
        CoMa\Helper\Cache::deleteCache('post');
        CoMa\Helper\Base::renderAdminNotice(__('Deleted Pages/Posts Cache', \CoMa\PLUGIN_NAME), \CoMa\WP_ADMIN_NOTICE_TYPE_SUCCESS);
    } else {
        CoMa\Helper\Base::renderAdminNotice(__('Deleting need to be accepted.', \CoMa\PLUGIN_NAME), \CoMa\WP_ADMIN_NOTICE_TYPE_ERROR);
    }

}

?>

<div class="wrap">
    <h1><?php echo __('Settings') . ' › ' . __(\CoMa\PLUGIN_TITLE_SHORT, \CoMa\PLUGIN_TITLE_SHORT); ?></h1>
</div>

<div>

    <form action='options.php' method='post'>

        <?php
        settings_fields( \CoMa\Helper\InstallOptions\GROUP_GENERAL );
        do_settings_sections( \CoMa\Helper\InstallOptions\GROUP_GENERAL);
        submit_button();
        ?>

    </form>

    <form method="post" action="">
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php echo __('Delete Pages/Posts Cache', \CoMa\PLUGIN_NAME); ?></th>
                <td><input type="checkbox" name="allowDeletePagesPostsCache" value="1"/>

                    <p class="description"
                       id="description"><?php echo __('Allowed deleting Pages/Posts Cache.', \CoMa\PLUGIN_NAME); ?></p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" class="button button-primary" name="deletePagePost"
                   value="<?php echo __('Delete Pages/Posts Cache', \CoMa\PLUGIN_NAME); ?>">
        </p>

        <?php
        //        submit_button();
        ?>

    </form>
<hr>
    <h3><?php echo __('Controller', \CoMa\PLUGIN_NAME); ?></h3>

    <form method="post" action="">
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php echo __('Delete Controllers', \CoMa\PLUGIN_NAME); ?></th>
                <td><input type="checkbox" name="allowDeleteControllers" value="1"/>

                    <p class="description"
                       id="description"><?php echo __('Allowed deleting all Controllers.', \CoMa\PLUGIN_NAME); ?></p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" class="button button-primary" name="deleteControllers"
                   value="<?php echo __('Delete Controllers', \CoMa\PLUGIN_NAME); ?>">
        </p>

        <?php
        //        submit_button();
        ?>

    </form>


</div>