<?php

if(\CoMa\Helper\Base::GET('dialog')) {

    $dialog= \CoMa\Helper\Base::GET('dialog');

    switch ($dialog) {
        case 'global-properties':
            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\GLOBAL_PROPERTIES)) {
            include(\CoMa\PLUGIN_PATH . 'dialogs/global-properties.php');
            }
            break;
        case 'page-properties':
            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\PAGE_PROPERTIES)) {
                include(\CoMa\PLUGIN_PATH . 'dialogs/page-properties.php');
            }
            break;
        case 'component-select':
            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_SELECT)) {
            include(\CoMa\PLUGIN_PATH . 'dialogs/component-select.php');
            }
            break;
        case 'area-edit':
            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\AREA_EDIT)) {
            include(\CoMa\PLUGIN_PATH . 'dialogs/area-edit.php');
            }
            break;
        case 'component-edit':
            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_EDIT)) {
            include(\CoMa\PLUGIN_PATH . 'dialogs/component-edit.php');
            }
            break;
    }

    exit();

}

?>