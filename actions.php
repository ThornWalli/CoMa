<?php

if (\CoMa\Helper\Base::POST('data') || CoMa\Helper\Base::GET('dialog') || CoMa\Helper\Base::getIsset('action')) {

    include_once(ABSPATH . 'wp-admin/includes/image.php');

    /**
     * @type wpdb $wpdb
     */
    global $wpdb;

    CoMa\Helper\Base::setupSession();


    if (\CoMa\Helper\Base::getIsset('action')) {

        $returnData = [
            'result' => 0,
            'log' => null
        ];

        switch (\CoMa\Helper\Base::GET('action')) {

            case 'clear-attachments':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\CACHE_CLEAR_ATTACHMENTS)) {
                    CoMa\Helper\Base::cleanUploadDirectory(ABSPATH . 'wp-content/uploads');
                }
                break;

            case 'prepare-attachments-refresh':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\CACHE_CLEAR_ATTACHMENTS)) {
                    CoMa\Helper\Base::cleanUploadDirectory(ABSPATH . 'wp-content/uploads');
                    $attachments = \CoMa\Helper\Base::getAttachments(\CoMa\Helper\Base::GET('type'));
                    $ids = [];
                    foreach ($attachments as $attachment) {
                        $ids[] = $attachment->ID;
                    }
                    $returnData['result'] = 1;
                    $returnData['attachments'] = $ids;
                }
                break;
            case 'get-attachments':
                $attachments = [];
                $media_query = new WP_Query(
                    [
                        'post_type' => 'attachment',
                        'post_status' => 'inherit',
                        'posts_per_page' => -1,
                        'post_mime_type' => \CoMa\Helper\Base::GET('type')
                    ]
                );
                $list = [];
                foreach ($media_query->posts as $image) {
                    $attachments[] = $image->ID;
                }
                $returnData['result'] = 1;
                $returnData['attachments'] = $attachments;
                break;
            case 'refresh-attachment':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\CACHE_CLEAR_ATTACHMENTS)) {
                    $id = \CoMa\Helper\Base::GET('id');
                    $fullsizepath = get_attached_file($id);
                    if (false === $fullsizepath || !file_exists($fullsizepath)) {
                        $returnData['result'] = 0;
                    } else {
                        $attach_data = wp_generate_attachment_metadata($id, $fullsizepath);
                        wp_update_attachment_metadata($id, $attach_data);
                        $returnData['result'] = 1;
                    }
                }
                break;
            case 'render-area':
                $id = \CoMa\Helper\Base::GET('area-id');
                $area = \CoMa\Helper\Component::getAreaById($id);
                global $post;
                $post = get_post($area->getPageId(true));
                $renderOptions = [];
                if (\CoMa\Helper\Base::isEditMode()) {
                    $renderOptions['edit'] = true;
                }
                $area->render($renderOptions);
                $returnData = false;
                break;
            case 'render-component':
                $id = \CoMa\Helper\Base::GET('component-id');
                $component = \CoMa\Helper\Component::getComponentById($id);
                global $post;
                $post = get_post($component->getPageId(true));
                $renderOptions = [];
                if (\CoMa\Helper\Base::isEditMode()) {
                    $renderOptions['edit'] = true;
                }
                $component->render($renderOptions);
                $returnData = false;
                break;
            case 'render-components':
                $id = \CoMa\Helper\Base::GET('area-id');
                $area = \CoMa\Helper\Component::getAreaById($id);
                $renderOptions = [];
                if (\CoMa\Helper\Base::isEditMode()) {
                    $renderOptions['edit'] = true;
                }
                foreach ($area->getChildrens() as $component) {
                    global $post;
                    $post = get_post($component->getPageId(true));
                    $component->render($renderOptions);
                }
                $returnData = false;
                break;
            case 'get-capabilities':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\WARRANTIES) || \CoMa\Helper\Base::isAdministrator()) {
                    $capabilities = [];
                    foreach (\CoMa\Helper\Base::$ROLE_CAPS as $capability) {
                        $capabilities[CoMa\PREFIX . '_' . $capability] = ['name' => CoMa\PREFIX . '_' . $capability, 'checked' => false];
                    }
                    if (\CoMa\Helper\Base::GET('filter') == 'all') {
                        $capabilities = \CoMa\Helper\Base::getAllCapabilities();
                    }
                    $returnCapabilities = [];
                    if (\CoMa\Helper\Base::GET('role')) {
                        $role = get_role(\CoMa\Helper\Base::GET('role'));
                        foreach ($capabilities as $key => $capability) {
                            $returnCapabilities[$key] = ['name' => $key, 'checked' => !!$role->capabilities[$key]];
                        }
                    } else {
                        foreach ($capabilities as $key => $capability) {
                            $returnCapabilities[$key] = ['name' => $key, 'checked' => false];
                        }
                    }
                    $returnData['result'] = 1;
                    $returnData['capabilities'] = $returnCapabilities;
                }
                break;

        }

    } else if (\CoMa\Helper\Base::GET('dialog')) {

        include(\CoMa\PLUGIN_PATH . 'dialogs/dialogs.php');

    } else {

        $postData = \CoMa\Helper\Base::POST('data');

        switch ($postData['action']) {

            case 'reset-cache':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\RESET_POST_CACHE)) {
                    \CoMa\Helper\Base::removePostCache($postData['id']);
                    $returnData['result'] = 1;
                }
            case 'global-edit-properties':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\GLOBAL_PROPERTIES_EDIT)) {
                    $properties = \CoMa\Helper\Base::decodeFormDialogProperties($postData['properties']);
                    \CoMa\Helper\Base::getGlobalProperties()->set($properties);
                    $returnData['result'] = 1;
                }
                break;
            case 'global-edit-property':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\GLOBAL_PROPERTIES_EDIT)) {
                    $properties = \CoMa\Helper\Base::decodeFormDialogProperties($postData['properties']);
                    $globalProperties = \CoMa\Helper\Base::getGlobalProperties()->get();
                    foreach ($properties as $name => $property) {
                        $globalProperties[$name] = $property;
                    }
                    \CoMa\Helper\Base::getGlobalProperties()->set($globalProperties);
                    $returnData['result'] = 1;
                }
                break;
            case 'page-edit-properties':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\PAGE_PROPERTIES_EDIT)) {
                    $properties = \CoMa\Helper\Base::decodeFormDialogProperties($postData['properties']);
                    \CoMa\Helper\Base::getPageProperties($postData['pageId'])->set($properties);
                    $returnData['result'] = 1;
                }
                break;
            case 'capabilities-save':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\WARRANTIES) || \CoMa\Helper\Base::isAdministrator()) {
                    $role = get_role($postData['role']);
                    foreach ($postData['capatilities'] as $key => $capability) {
                        $role->add_cap($key, $capability);
                    }
                    $returnData['result'] = 1;
                }
                break;
            case 'area-select':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\AREA)) {
                    \CoMa\Helper\Base::setSession('page-id', $postData['pageId']);
                    $area = null;
                    if ($postData['id']) {
                        $area = \CoMa\Helper\Component::getAreaById($postData['id']);
                    }
//                    else if ($postData['position']) {
//                        $area = \CoMa\Helper\Component::getAreaByPosition($postData['position']);
//                    }
                    $class = null;
                    if ($postData['class']) {
                        $class = $postData['class'];
                    }
                    $position = $postData['position'];
                    if (!$postData['position']) {
                        $position = $area->getPosition();
                    }
                    if (!$area || !$area->getId()) {
                        if ($postData['parentId']) {
                            $postData['parentId'] = esc_sql($postData['parentId']);
                        } else {
                            $postData['pageId'] = esc_sql($postData['pageId']);
                        }
                        \CoMa\Helper\Controller::saveController([
                            'page_id' => \CoMa\Helper\Base::getSession('page-id'),
                            'parent_id' => $postData['parentId'],
                            'type_id' => CoMa\TYPE_AREA,
                            'class' => \CoMa\Helper\Base::performClassName($class),
                            'properties' => [],
                            'position' => $position
                        ]);
                        // Erstelltes Area abrufen
                        $returnData['id'] = $wpdb->insert_id;
                    } else {
                        $returnData['id'] = $area->getId();
                    }
                    \CoMa\Helper\Base::setSession('area-id', $returnData['id']);
                    \CoMa\Helper\Base::setSession('component-id', null);
                    $returnData['result'] = 1;
                }
                break;
            case 'area-edit':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\AREA_EDIT)) {
                    $returnData['id'] = null;
                    $properties = esc_sql(json_encode($postData['properties']));
                    if (isset($postData['id']) && $postData['id']) {
                        $returnData['id'] = \CoMa\Helper\Controller::saveController([
                            'id' => $postData['id'],
                            'properties' => $postData['properties']
                        ]);
                    }
                    $returnData['result'] = $returnData['id'] ? 1 : 0;
                }
                break;
            case 'area-copy-component':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_COPY)) {
                    $returnData['id'] = null;
                    $component = \CoMa\Helper\Component::getComponentById($postData['componentId']);
                    if ($component) {
                        $clone = \CoMa\Helper\Controller::cloneController($component);
                        \CoMa\Helper\Controller::saveController([
                            'id' => $clone->getId(),
                            'rank' => \CoMa\Helper\Component::getNextComponentRank($clone->getParentId())
                        ]);
                        if ($clone) {
                            $returnData['id'] = $clone->getId();
                        }
                    }
                    $returnData['result'] = $returnData['id'] ? 1 : 0;
                }
                break;
            case 'component-select':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_SELECT)) {
                    if (isset($postData['areaId'])) {
                        \CoMa\Helper\Base::setSession('area-id', $postData['areaId']);
                    } else {
                        \CoMa\Helper\Base::setSession('component-id', $postData['id']);
                        \CoMa\Helper\Base::setSession('area-id', null);
                    }
                    \CoMa\Helper\Base::setSession('component-class', stripslashes($postData['class']));
                    $returnData['result'] = 1;
                }
                break;
            case 'component-edit':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_EDIT)) {
                    if (isset($postData['id']) && $postData['id']) {
                        $returnData['id'] = \CoMa\Helper\Controller::saveController([
                            'id' => $postData['id'],
                            'properties' => \CoMa\Base\Controller::parseProperties($postData['properties'])
                        ]);

                        $component = \CoMa\Helper\Component::getComponentById($postData['id']);
                    } else {
                        $rank = \CoMa\Helper\Component::getNextComponentRank($postData['areaId']);
                        $returnData['id'] = \CoMa\Helper\Controller::saveController([
                            'rank' => $rank,
                            'parent_id' => $postData['areaId'],
                            'type_id' => CoMa\TYPE_COMPONENT,
                            'class' => \CoMa\Helper\Base::performClassName($postData['class']),
                            'properties' => \CoMa\Base\Controller::parseProperties($postData['properties']),
                            'position' => $postData['position']

                        ]);
                    }
                    $returnData['result'] = $returnData['id'] ? 1 : 0;
                }
                break;
            case 'component-remove':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_REMOVE)) {
                    if ($postData['ids']) {
                        foreach ($postData['ids'] as $id) {
                            $component = \CoMa\Helper\Component::getComponentById($id);
                            $component->remove();
                        }
                    } else {
                        $component = \CoMa\Helper\Component::getComponentById($postData['id']);
                        $component->remove();
                    }
                    $returnData['result'] = 1;
                }
                break;
            case 'component-disabled':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_DISABLE)) {
                    $wpdb->update(
                        $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS),
                        ['disabled' => esc_sql((integer)$postData['disabled'])],
                        ['id' => $postData['id']]
                    );
                    $returnData['result'] = 1;
                }
                break;
            case 'component-set-ranks':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_SET_RANK)) {
                    if (is_array($postData['ranks'])) {
                        foreach ($postData['ranks'] as $rank => $id) {
                            $wpdb->update(
                                $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS),
                                ['rank' => esc_sql(((integer)$rank))],
                                ['id' => $id]
                            );
                        }
                        $returnData['result'] = 1;
                    }
                }
                break;
            case 'component-rank-up':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_RANK_UP)) {
                    $wpdb->update(
                        $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS),
                        ['rank' => esc_sql(((integer)$postData['rank']) + 1)],
                        ['id' => $postData['id']]
                    );
                    $returnData['result'] = 1;
                }
                break;
            case 'component-rank-down':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_RANK_DOWN)) {
                    $wpdb->update(
                        $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS),
                        ['rank' => esc_sql((integer)$postData['rank'] - 1)],
                        ['id' => $postData['id']]
                    );
                    $returnData['result'] = 1;
                }
                break;
            case 'component-edit-property':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_PROPERTIES_EDIT)) {
                    $controller = \CoMa\Helper\Controller::getControllerById($postData['id']);
                    $properties = json_decode($controller['properties'], true);
                    $properties[$postData['name']] = stripslashes($postData['value']);
                    \CoMa\Helper\Controller::saveController([
                        'id' => $postData['id'],
                        'properties' => $properties
                    ]);
                    $returnData['result'] = 1;
                }
                break;
            case 'component-rename-property':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_PROPERTIES_RENAME)) {
                    $controller = \CoMa\Helper\Controller::getControllerById($postData['id']);
                    $properties = json_decode(str_replace('\"', '"', $controller['properties']), true);
                    $properties[$postData['name']] = $properties[$postData['lastName']];
                    unset($properties[$postData['lastName']]);
                    $wpdb->update(
                        $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS),
                        [
                            'properties' => json_encode($properties)
                        ],
                        ['id' => $postData['id']]
                    );
                    $returnData['result'] = 1;
                }
                break;
            case 'component-remove-properties':
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_PROPERTIES_REMOVE)) {

                    foreach ($postData['ids'] as $id => $properties_) {
                        $controller = \CoMa\Helper\Controller::getControllerById($id);
                        $properties = json_decode(str_replace('\"', '"', $controller['properties']), true);
                        foreach ($properties_ as $property) {
                            unset($properties[$property]);
                        }
                        $wpdb->update(
                            $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS),
                            ['properties' => json_encode($properties)],
                            ['id' => $id]
                        );
                    }
                    $returnData['result'] = 1;
                }
                break;
            case 'get-components':
                $controllers = [];
                if (array_key_exists('pageId', $postData)) {
                    if ($postData['pageId'] == 0) {
                        $controllers = \CoMa\Helper\Controller::getControllersByPageId($postData['pageId']);
                    } else if ($postData['pageId']) {
                        $controllers = \CoMa\Helper\Controller::getControllersByPageId($postData['pageId']);
                    }
                } else {
                    $controllers = \CoMa\Helper\Controller::getControllersByParentId($postData['parentId']);
                }
                foreach ($controllers as $key => $controller) {
                    $controller['properties'] = json_decode(stripslashes($controller['properties']), true);
                    $controllers[$key] = $controller;
                }
                $returnData['controllers'] = $controllers;
                $returnData['result'] = 1;
                break;
            case 'get-component-properties':
                $controller = \CoMa\Helper\Controller::parseController(\CoMa\Helper\Controller::getControllerById($postData['id']));
                $returnData['result'] = 1;
                $returnData['properties'] = \CoMa\Helper\Base::encodeFormDialogProperties($controller->getProperties());
                break;
        }
    }

    if ($returnData) {
        $returnData['logs'] = \CoMa\Helper\Base::getLogs();
        echo json_encode($returnData);
    }

    exit();
}

?>
