<div class="wrap">
    <h2><?php echo __(\CoMa\PLUGIN_TITLE_SHORT,\CoMa\PLUGIN_NAME).' - '.__('Overview'); ?></h2>
</div>

<div>
<br />
    <article>

        <?php

        $pluginData = get_plugin_data(\CoMa\PLUGIN_PATH.'coma.php');

        ?>

        <header>
            <img src="<?php echo \CoMa\PLUGIN_URL . 'assets/logo.png'; ?>" />
            <h1><?php echo $pluginData['Title']; ?></h1>
        </header>

        <p>
            <?php echo __('Version').': '.$pluginData['Version']; ?> (DB-Version: <?php echo \CoMa\Helper\Base::getWPOption(\CoMa\WP\Options\DB_VERSION); ?>)
        </p>

        <p>
            <?php echo $pluginData['Description']; ?><br />
        </p>

    </article>




</div>