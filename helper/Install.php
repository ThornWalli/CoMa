<?php

namespace CoMa\Helper;

class Install
{

    public static function options()
    {
        InstallOptions::init();
    }

    public static function properties()
    {

//        add_filter(\CoMa\WP\Filter\GLOBAL_PROPERTIES_DIALOG, function ($propertyDialog) {
//            /**
//             * @type PropertyDialog $propertyDialog
//             */
////            $tab = $propertyDialog->getTab();
////            $tab->addTextField('example', 'Example');
//            return $propertyDialog;
//        });

//        add_filter(\CoMa\WP\Filter\PAGE_PROPERTIES_DIALOG, function ($propertyDialog, $pageId) {
//            /**
//             * @type PropertyDialog $propertyDialog
//             */
//            switch (get_page_template_slug($pageId)) {
//                /*
//                 * Hier können die Seiten Eigenschaten angegeben werden.
//                 */
//                default:
////                    $tab = $propertyDialog->getTab();
////                    $tab->addTextField('example', 'Example');
//                    break;
//            }
//            return $propertyDialog;
//        }, 10, 2);

    }

    public static function editor()
    {
        add_action(\CoMa\WP\Action\EDITOR_HTML, 'CoMa\Helper\InstallEditor::editor_html');
        add_filter('tiny_mce_before_init', 'CoMa\Helper\InstallEditor::tiny_mce_before_init', 10, 2);
        add_filter('quicktags_settings', 'CoMa\Helper\InstallEditor::quicktags_settings', 10, 2);
    }

    public static function sql()
    {
        global $CONTENT_MANAGER_DB_VERSION;

        function install()
        {
            global $wpdb, $CONTENT_MANAGER_DB_VERSION;

            if (Base::getWPOption(\CoMa\WP\Options\DB_VERSION) != $CONTENT_MANAGER_DB_VERSION) {

                $charset_collate = $wpdb->get_charset_collate();
                $controllersTableName = \DB_NAME . '.' . $wpdb->prefix . Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS);
                $pagesTableName = $wpdb->prefix . Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_PAGES);
                $wpOptionsTableName = \DB_NAME . '.' . $wpdb->prefix . 'options';

                $createQueries = [];
                $queries = [];
                $nextVersion = null;

                switch (Base::getWPOption(\CoMa\WP\Options\DB_VERSION)) {
                    case '0.0.4':
                        $queries[] = "ALTER TABLE $controllersTableName CHANGE COLUMN page_id page_id INT(255) UNSIGNED NOT NULL DEFAULT '0' AFTER id;";
                        $queries[] = "ALTER TABLE $controllersTableName CHANGE COLUMN parent_id parent_id INT(255) UNSIGNED NOT NULL DEFAULT '0' AFTER page_id;";;
                        $nextVersion = $CONTENT_MANAGER_DB_VERSION;
                        break;
                    case '0.0.3':
                        $controllers = Controller::getAllControllers();
                        foreach ($controllers as $controllerData) {
                            $controllerData['properties'] = str_replace('_linkType', '_link_type', $controllerData['properties']);
                            $controllerData['properties'] = str_replace('_linkInternalValue', '_link_internal_value', $controllerData['properties']);
                            $controllerData['properties'] = str_replace('_linkExternalValue', '_link_external_value', $controllerData['properties']);
                            $controllerData['properties'] = str_replace('_linkTitle', '_link_title', $controllerData['properties']);
                            $controllerData['properties'] = str_replace('_linkTarget', '_link_target', $controllerData['properties']);
                            $controller = Controller::parseController($controllerData);
                            Controller::saveController(['id' => $controller->getId(), 'properties' => $controller->getProperties()]);
                        }
                        $nextVersion = '0.0.4';
                        break;
                    case '0.0.2':
                        $properties = [
                            \CoMa\WP\Options\CACHE_MEDIA_JPEG_QUALITY,
                            \CoMa\WP\Options\DEBUG_SHOW_ELEMENTS,
                            \CoMa\WP\Options\DEBUG_LIVERELOAD,
                            \CoMa\WP\Options\CACHE_PAGE,
                            \CoMa\WP\Options\CACHE_POST,
                            \CoMa\WP\Options\CACHE_PATH,
                            \CoMa\WP\Options\CACHE_MEDIA_PNG_QUALITY,
                            \CoMa\WP\Options\CACHE_MEDIA_JPEG_QUALITY,
                            \CoMa\WP\Options\USE_WP_PAGE_POST_REVISION,
                            \CoMa\WP\Options\GLOBAL_PROPERTIES
                        ];
                        foreach ($properties as $property) {
                            $queries[] = "UPDATE $wpOptionsTableName SET option_name='" . Base::getPrefixedSQLName($property) . "' WHERE  option_name = 'cm_" . $property . "';";
                        }
                        $nextVersion = '0.0.3';
                        break;
                    case '0.0.1':
                        $queries[] = "ALTER TABLE $controllersTableName ADD COLUMN disabled TINYINT(1) NOT NULL DEFAULT '0' AFTER rank;";
                        $nextVersion = '0.0.2';
                        break;
                    default:
                        $queries[] = "CREATE TABLE $controllersTableName (
                                            id INT(255) NOT NULL AUTO_INCREMENT,
                                            page_id INT(255) UNSIGNED NOT NULL DEFAULT '0',
                                            parent_id INT(255) UNSIGNED NOT NULL DEFAULT '0',
                                            type_id INT(10) UNSIGNED NOT NULL,
                                            class VARCHAR(255) NOT NULL,
                                            properties TEXT NOT NULL,
                                            position VARCHAR(45) NULL DEFAULT NULL,
                                            rank INT(25) UNSIGNED NOT NULL DEFAULT '0',
                                            disabled TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
                                            PRIMARY KEY (id)
                                        ) $charset_collate;";
                        $queries[] = "CREATE TABLE $pagesTableName(
                                            page_id INT(255) UNSIGNED NULL DEFAULT NULL,
                                            properties TEXT NULL
                                        ) COMMENT='Seiten (Page/Post) Eigenschaften'
                                          $charset_collate;";
                        $nextVersion = $CONTENT_MANAGER_DB_VERSION;
                        break;
                }

                if ($nextVersion != null) {
                    if (count($queries) > 0) {
                        foreach ($queries as $query) {
                            $wpdb->query($query);
                        }
                    }
                    if (count($createQueries) > 0) {
                        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                        foreach ($createQueries as $query) {
                            dbDelta($query);
                        }
                    }
                    Base::setWPOption(\CoMa\WP\Options\DB_VERSION, $nextVersion);
                    install();
                }

            }

        }

        add_action('plugins_loaded', function () {
            global $CONTENT_MANAGER_DB_VERSION;
            if (Base::getWPOption(\CoMa\WP\Options\DB_VERSION) != $CONTENT_MANAGER_DB_VERSION) {
                install();
            }
        });
//        register_activation_hook(__FILE__, 'install');
//        register_activation_hook(__FILE__, function () {
//            global $wpdb;
//        });

    }

    public static function warranties()
    {

        add_action('load-plugins.php', function () {
            global $pagenow;

//            "administrator" Administrator
//            "editor" Editor
//            "author" Author
//            "contributor" Contributor
//            "subscriber" Subscriber

            /**
             * Default caps
             */
            if ('plugins.php' == $pagenow) {
                if (isset($_GET['activate']) || isset($_GET['action']) && $_GET['action'] == 'activate') {
                    $administratorCaps = Base::$ROLE_CAPS;
                    $administratorRole = get_role('administrator');
                    foreach ($administratorCaps as $cap) {
                        $administratorRole->add_cap(\CoMa\PREFIX . '_' . $cap, true);
                    }

                    $editorCaps = Base::$ROLE_CAPS;
                    $editorRole = get_role('editor');
                    foreach ($editorCaps as $cap) {
                        $editorRole->add_cap(\CoMa\PREFIX . '_' . $cap, true);
                    }

                } else if (isset($_GET['deactivate']) || isset($_GET['action']) && $_GET['action'] == 'deactivate') {
                    foreach (Base::getEditableRoles() as $key => $roleData) {
                        $role = get_role($key);
                        foreach (Base::$ROLE_CAPS as $cap) {
                            $role->remove_cap(\CoMa\PREFIX . '_' . $cap);
                        }
                    }
                }
            }
        });


    }

    public static function languages()
    {

        add_action('init', function () {
            load_plugin_textdomain(\CoMa\PLUGIN_NAME, false, \CoMa\PLUGIN_NAME . '/languages/');
        });
    }

    public static function theme()
    {

        add_action('customize_register', function ($wp_customize) {

            $wp_customize->add_section(Base::getPrefixedName('general'), [
                'title' => __(\CoMa\PLUGIN_TITLE_SHORT, \CoMa\PLUGIN_NAME),
                'priority' => 1,
            ]);
            $wp_customize->add_setting(Base::getPrefixedName(\CoMa\WP\THEME_MOD\CUSTOMIZE_PREVIEW_MODE), [
                'default' => 'preview',
                'transport' => 'refresh'
            ]);

            $wp_customize->add_control(Base::getPrefixedName(\CoMa\WP\THEME_MOD\CUSTOMIZE_PREVIEW_MODE), [
                'label' => __('Mode?', \CoMa\PLUGIN_NAME),
                'section' => Base::getPrefixedName('general'),
                'type' => 'radio',
                'choices' => [
                    'preview' => __('Preview', \CoMa\PLUGIN_NAME),
                    'author' => __('Author', \CoMa\PLUGIN_NAME)
                ]
            ]);
            $wp_customize->add_setting(Base::getPrefixedName(\CoMa\WP\Options\DEBUG_SHOW_ELEMENTS), [
                'default' => 'false',
                'type' => 'option',
                'transport' => 'refresh'
            ]);

            $wp_customize->add_control(Base::getPrefixedName(\CoMa\WP\Options\DEBUG_SHOW_ELEMENTS), [
                'label' => __('Show Elements?', \CoMa\PLUGIN_NAME),
                'section' => Base::getPrefixedName('general'),
                'type' => 'checkbox'
            ]);

        });
    }

    public static function adminBar()
    {
        add_action('wp_before_admin_bar_render', function () {
            global $wp_admin_bar;

            if (!is_admin()) {

                $isPreview = Base::isPreview();

                $cached = Cache::isCached(Base::getPageId());
                if ($isPreview) {
                    $title = __('Author', \CoMa\PLUGIN_NAME);
                } else {
                    $title = __('Preview', \CoMa\PLUGIN_NAME);
                }
                $title .= ' <span>(' . (get_post_type(Base::getPageId()) == 'post' ? __('Post', \CoMa\PLUGIN_NAME) : __('Page', \CoMa\PLUGIN_NAME));
                if ($cached) {
                    $title .= ' <span>' . __('Is cached', \CoMa\PLUGIN_NAME) . '</span>';
                }
                $title .= ')</span>';

                $wp_admin_bar->add_node([
                    'id' => 'coma-mode-toggle',
                    'meta' => ['class' => 'coma-mode-toggle'],
                    'title' => '<span class="ab-icon"></span><span class="ab-label">' . $title . '</span>',
                    'href' => '?' . \CoMa\PREFIX . '-mode=' . ($isPreview ? 'author' : 'preview')
                ]);

                if (Base::isEditMode()) {

                    $wp_admin_bar->add_node([
                        'parent' => 'coma-mode-toggle',
                        'id' => 'coma-mode-toggle-secondary',
                        'meta' => ['class' => 'coma-mode-toggle-secondary'],
                        'title' => $title,
                        'href' => '?' . \CoMa\PREFIX . '-mode=' . ($isPreview ? 'author' : 'preview')
                    ]);


                    if (Base::roleHasCap(\CoMa\Roles\PAGE_PROPERTIES) && Base::getPageId()) {

                        $wp_admin_bar->add_node([
                            'parent' => 'coma-mode-toggle',
                            'id' => 'coma-page-properties',
                            'meta' => ['class' => 'coma-page-properties'],
                            'title' => __('Page Properties', \CoMa\PLUGIN_NAME),
                            'href' => '#'
                        ]);

                    }

                    if (Base::roleHasCap(\CoMa\Roles\GLOBAL_PROPERTIES)) {

                        $wp_admin_bar->add_node([
                            'parent' => 'coma-mode-toggle',
                            'id' => 'coma-global-properties',
                            'meta' => ['class' => 'coma-global-properties'],
                            'title' => __('Global Properties', \CoMa\PLUGIN_NAME),
                            'href' => '#'
                        ]);

                    }

                    if ($cached && Base::roleHasCap(\CoMa\Roles\RESET_POST_CACHE)) {

                        $wp_admin_bar->add_node([
                            'parent' => 'coma-mode-toggle',
                            'id' => 'coma-cache-reset',
                            'meta' => ['class' => 'coma-cache-reset'],
                            'title' => __('Cache Reset', \CoMa\PLUGIN_NAME),
                            'href' => '#'
                        ]);

                    }

                }

            }

        });
    }

    public static function wpImports()
    {
        wp_enqueue_style('wp-admin');
        wp_enqueue_style('media');

        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox', 'jquery');

        wp_enqueue_media();

        if (user_can_richedit()) {
            wp_enqueue_script('tiny_mce', get_option('siteurl') . '/wp-includes/js/tinymce/tinymce.min.js', 10001);
            wp_enqueue_script('wplink');
            wp_enqueue_script('wpdialogs-popup');
            wp_enqueue_script('editor');
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script(
            'iris',
            admin_url('js/iris.min.js'),
            ['jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'],
            false,
            1
        );
        wp_enqueue_script(
            'wp-color-picker',
            admin_url('js/color-picker.min.js'),
            ['iris'],
            false,
            1
        );
        wp_localize_script('wp-color-picker', 'wpColorPickerL10n', [
            'clear' => __('Clear'),
            'defaultString' => __('Default'),
            'pick' => __('Select Color')
        ]);

    }

    /**
     * Import CoMa Style & Script
     */
    public static function imports()
    {

        // CoMa Style

        wp_enqueue_style('coma-wp', \CoMa\PLUGIN_URL .'css/wp.css', [], \CoMa\VERSION);
        wp_enqueue_style('coma-style', \CoMa\PLUGIN_URL . 'css/style.css', [], \CoMa\VERSION);

        // CoMa Script

        if (\CoMa\MIN_JS) {

            // Productive

            wp_enqueue_script('coma', \CoMa\PLUGIN_URL . 'js/main.js', ['underscore'], \CoMa\VERSION, true);

            $data = 'var coma = { require: { baseUrl: \'\' } };';
            wp_add_inline_script('coma', $data, 'before');

        } else {

            // Development
            wp_register_script('requirejs', \CoMa\PLUGIN_URL . 'js/require.js', [], \CoMa\VERSION, true);
            wp_enqueue_script('coma', \CoMa\PLUGIN_URL . 'src/js/config.js', ['underscore', 'requirejs'], \CoMa\VERSION, true);

            $data = 'var coma = { baseUrl:  "' . ((\CoMa\MIN_JS) ? \CoMa\PLUGIN_URL . 'js/' : \CoMa\PLUGIN_URL . 'src/js') . '" };';

            wp_add_inline_script('requirejs', $data, 'before');

        }

    }

}

?>