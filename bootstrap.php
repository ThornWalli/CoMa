<?php

include(plugin_dir_path(__FILE__) . '/bootstrap/plugin.php');
include(plugin_dir_path(__FILE__) . '/bootstrap/template.php');

CoMa\PluginBoostrap::init(plugin_dir_path(__FILE__));
CoMa\TemplateBoostrap::init(\CoMa\THEME_PATH.'/coma');

?>