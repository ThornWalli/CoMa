<?php
/*
Plugin Name: CoMa (Content-Manager)
Plugin URI: http://coma.website/
Description: Erweitert das bearbeiten des Frontends um eine neue Oberfläche zum anlegen von Inhalt. <strong>Zur verwendung wird ein CoMa angepasstes Design benötigt.</strong>
Version: 0.1.0.0
Author: Thorn-Welf Walli
Author URI: http://coma.website/
Min WP Version: 1.5
Max WP Version: 2.0.4
Text Domain: coma
*/

include(plugin_dir_path(__FILE__) . '/vars.php');
include(\CoMa\PLUGIN_PATH . '/bootstrap.php');

function CoMaPlugin_Setup()
{

    function setupAdmin()
    {

        add_action('admin_menu', 'menu');
        function menu()
        {

            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\OPTIONS) || CoMa\Helper\Base::isAdministrator()) {
                add_options_page(
                    __(\CoMa\PLUGIN_TITLE_SHORT, \CoMa\PLUGIN_NAME),
                    __(\CoMa\PLUGIN_TITLE_SHORT, \CoMa\PLUGIN_NAME),
                    'manage_options', CoMa\PREFIX . '_options', function () {
                    include(\CoMa\PLUGIN_PATH . '/pages/options.php');
                });
            }

            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\CONTENT_MANAGER) || CoMa\Helper\Base::isAdministrator()) {

                add_menu_page(\CoMa\PLUGIN_NAME, __(\CoMa\PLUGIN_TITLE_SHORT, \CoMa\PLUGIN_NAME), \CoMa\Helper\Base::getCapName('content_manager'), 'coma', 'overview', \CoMa\PLUGIN_URL . 'assets/icon.svg');
                add_submenu_page('coma', __(\CoMa\PLUGIN_TITLE_SHORT, \CoMa\PLUGIN_NAME) . ' ' . __('Overview'), __('Overview'), \CoMa\Helper\Base::getCapName('content_manager'), 'coma', 'overview');
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\CACHE)) {
                    add_submenu_page('coma', __(\CoMa\PLUGIN_NAME, \CoMa\PLUGIN_NAME) . ' ' . __('Cache', \CoMa\PLUGIN_NAME), __('Cache', \CoMa\PLUGIN_NAME), \CoMa\Helper\Base::getCapName('content_manager'), '\coma\cache', function () {

                        if (!CoMa\Helper\Base::roleHasCap(\CoMa\Roles\CACHE)) {
                            wp_die(__('You do not have sufficient permissions to access this page.'));
                        }
                        include(\CoMa\PLUGIN_PATH . '/pages/cache.php');
                    });
                }
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\WARRANTIES) || CoMa\Helper\Base::isAdministrator()) {
                    add_submenu_page('coma', __(\CoMa\PLUGIN_NAME, \CoMa\PLUGIN_NAME) . ' ' . __('Warranties', \CoMa\PLUGIN_NAME) . ' (' . __('Roles', \CoMa\PLUGIN_NAME) . ')', __('Warranties', \CoMa\PLUGIN_NAME) . ' (' . __('Roles', \CoMa\PLUGIN_NAME) . ')', \CoMa\Helper\Base::getCapName('warranties'), '\coma\warranties', function () {

                        if (!CoMa\Helper\Base::roleHasCap(\CoMa\Roles\WARRANTIES) && !CoMa\Helper\Base::isAdministrator()) {
                            wp_die(__('You do not have sufficient permissions to access this page.'));
                        }
                        include(\CoMa\PLUGIN_PATH . '/pages/warranties.php');
                    });
                }
                if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\CONTROLLER_BROWSER)) {
                    add_submenu_page('coma', __(\CoMa\PLUGIN_NAME, \CoMa\PLUGIN_NAME) . ' ' . __('Controller-Browser', \CoMa\PLUGIN_NAME), __('Controller-Browser', \CoMa\PLUGIN_NAME), \CoMa\Helper\Base::getCapName('controller_browser'), '\coma\controller_browser', function () {
                        if (!CoMa\Helper\Base::roleHasCap(\CoMa\Roles\CONTROLLER_BROWSER)) {
                            wp_die(__('You do not have sufficient permissions to access this page.'));
                        }
                        include(\CoMa\PLUGIN_PATH . '/pages/controllerBrowser.php');
                    });
                }
            }

        }

        function overview()
        {
            include(\CoMa\PLUGIN_PATH . '/pages/overview.php');
        }

    }

    function setupInclude()
    {

        /**
         * Löscht alle Controller die zu einer Seite gehören.
         * @param $id
         */
        add_action('before_delete_post', function ($id) {
            foreach (\CoMa\Helper\Controller::getControllersByPageId($id) as $controller) {
                $controller = \CoMa\Helper\Controller::parseController($controller);
                $controller->remove();
                CoMa\Helper\Base::getPageProperties($id)->remove();
            }
        });

        function getRowActions($actions, $post)
        {
            $url = get_permalink($post->ID);
            $actions['\CoMa\author_mode'] = '<a href="' . \CoMa\Helper\Base::addPreviewGetArg($url, false) . '" class="coma-author-mode">' . __('Author mode', \CoMa\PLUGIN_NAME) . '</a>';
            $actions['\CoMa\preview_mode'] = '<a href="' . \CoMa\Helper\Base::addPreviewGetArg($url, true) . '" class="coma-preview-mode">' . __('Preview mode', \CoMa\PLUGIN_NAME) . '</a>';
            return $actions;
        }

        add_filter('page_row_actions', function ($actions, $post) {
            return getRowActions($actions, $post);
        }, 10, 2);
        add_filter('post_row_actions', function ($actions, $post) {
            return getRowActions($actions, $post);
        }, 10, 2);

        add_action('wp_head', function () {

            if (is_customize_preview()) {
                \CoMa\Helper\Base::setEditMode(false);
                if (get_theme_mod(\CoMa\Helper\Base::getPrefixedName(\CoMa\WP\THEME_MOD\CUSTOMIZE_PREVIEW_MODE), 'preview') == 'author') {
                    \CoMa\Helper\Base::setEditMode(true);
                }
            }

            if (\CoMa\Helper\Base::isEditMode()) {

                \CoMa\Helper\Install::wpImports();
                \CoMa\Helper\Install::imports();

                CoMa\Helper\Base::setSession('page-id', CoMa\Helper\Base::getPageId()); // ID der aktuellen Seite
                if (!is_admin()) {
                    global $CONTENT_MANAGER_PAGE_AREAS;
                    // Ruft alle schon registrierten Areas auf der Seite ab.
                    $CONTENT_MANAGER_PAGE_AREAS = \CoMa\Helper\Base::getAreasByPage(\CoMa\Helper\Base::getSession('page-id'));
                }

            }
        }, 100000);




        add_action('wp_footer', function ()
        {
            if (\CoMa\Helper\Base::isEditMode()) {
                include(\CoMa\PLUGIN_PATH . '/footer.php');
            }
        }, 100000);

        add_action('admin_head', function () {
            \CoMa\Helper\Install::imports();
        }, 100000);

        if (!is_admin()) {
            add_filter('post_link', '\CoMa\Helper\Base::addPreviewGetArg');
            add_filter('home_url', '\CoMa\Helper\Base::addPreviewGetArg');
        }
        add_filter('the_permalink', '\CoMa\Helper\Base::addPreviewGetArg');

    }

    CoMa\Helper\Install::languages();
    CoMa\Helper\Install::sql();
    CoMa\Helper\Install::warranties();

    add_action('admin_init', function () {
        \CoMa\Helper\Install::options();
    });

    add_filter('body_class', function ($classes) {
        if (\CoMa\Helper\Base::isEditMode()) {
            $classes[] = 'coma-edit-mode';
        } else {
            $classes[] = 'coma-preview-mode';
        }
        if (\CoMa\Helper\Base::getWPOption(\CoMa\WP\Options\DEBUG_SHOW_ELEMENTS)) {
            $classes[] = 'coma-debug';
            $classes[] = 'coma-debug-show-elements';
        }
        return $classes;
    });

    add_action('init', function () {

        \CoMa\Helper\Base::setEditMode(
            is_user_logged_in() && (
                is_admin() ||
                \CoMa\Helper\Base::GET('edit-mode') ||
                preg_match("/author/", \CoMa\Helper\Base::GET('mode'))
            )
        );

        \CoMa\Helper\Cache::init();
        \CoMa\Helper\Base::setupSession();

        if (!\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\CONTENT_MANAGER)) {
            return;
        }

        \CoMa\Helper\Debug::init();
        \CoMa\Helper\Revision::init();
        \CoMa\Helper\Install::adminBar();

        if (is_admin() || CoMa\Helper\Base::isEditMode()) {

            CoMa\Helper\Install::theme();

            if (\CoMa\Helper\Base::isEditMode()) {

                if (get_theme_support(\CoMa\THEME_SUPPORT_NAME)) {
                    require_once(ABSPATH . 'wp-admin/includes/screen.php');
                    setupInclude();
                }

            }

            CoMa\Helper\Install::properties();
            CoMa\Helper\Install::warranties();

            if (get_theme_support(\CoMa\THEME_SUPPORT_NAME)) {
                CoMa\Helper\Install::editor();
            }

            if (is_admin()) {
                // Admin Interface
                setupAdmin();

                include(\CoMa\PLUGIN_PATH . 'actions.php');
            }

        }


    });

}

CoMaPlugin_Setup();

?>