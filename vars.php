<?php

namespace CoMa {

   const VERSION = '0.2.0.0';
   const DB_VERSION = '0.0.5';

   const PLUGIN_TITLE = 'CoMa (Content-Manager)';
   const PLUGIN_TITLE_SHORT = 'CoMa';

   const THEME_SUPPORT_NAME = 'coma';
   const PLUGIN_NAME = 'coma';

   const TYPE_AREA = 1;
   const TYPE_COMPONENT = 2;

   define('CoMa\ADMIN_URL', get_admin_url());
   const SESSION_PREFIX = 'coma';

   const PREFIX = 'coma';
   const SQL_PREFIX = 'coma';

   const MIN_JS = false;

   define('CoMa\DEEP_MODAL', PREFIX . '-dialog');
   define('CoMa\DEEP_COMPONENT', PREFIX . '-component');
   define('CoMa\DEEP_AREA', PREFIX . '-area');

   define('CoMa\PLUGIN_URL', plugin_dir_url(__FILE__));
   define('CoMa\PLUGIN_PATH', plugin_dir_path(__FILE__));
   define('CoMa\PLUGIN_TEMPLATE_PATH', PLUGIN_PATH . 'template/');
   define('CoMa\THEME_PATH', get_template_directory());
   define('CoMa\THEME_URL', get_template_directory_uri());
   define('CoMa\THEME_TEMPLATE_PATH', get_template_directory() . '/coma/template');
   define('CoMa\DEFAULT_CACHE_PATH', PLUGIN_PATH . 'cache');

   define('CoMa\IS_EDIT_MODE', false);

   const WP_ADMIN_NOTICE_TYPE_SUCCESS = 'notice-success';
   const WP_ADMIN_NOTICE_TYPE_WARNING = 'notice-warning';
   const WP_ADMIN_NOTICE_TYPE_ERROR = 'notice-error';
   const WP_ADMIN_NOTICE_TYPE_INFO = 'notice-info';

   const SQL_TABLE_NAME_CONTROLLERS = 'controllers';
   const SQL_TABLE_NAME_PAGES = 'pages';

   global $CONTENT_MANAGER_PAGE_AREAS, $CONTENT_MANAGER_PARENT_COMPONENT, $CONTENT_MANAGER_DB_VERSION;

   $CONTENT_MANAGER_DB_VERSION = '0.0.5';

   $CONTENT_MANAGER_PARENT_COMPONENT = null;
   $CONTENT_MANAGER_PAGE_AREAS = [];

}

namespace CoMa\WP\Action {


   define('CoMa\WP\Action\EDITOR_HTML', PREFIX . '_editor_html');
   define('CoMa\WP\Action\BEFORE_RENDER', PREFIX . '_before_render_controller');
   define('CoMa\WP\Action\AFTER_RENDER', PREFIX . '_after_render_controller');

}

namespace CoMa\WP\Filter {

   define('CoMa\WP\Filter\PAGE_PROPERTIES_DIALOG', PREFIX . '_page_properties_dialog');
   define('CoMa\WP\Filter\GLOBAL_PROPERTIES_DIALOG', PREFIX . '_global_properties_dialog');

}

namespace CoMa\WP\THEME_MOD {

   const CUSTOMIZE_PREVIEW_MODE = 'customize_preview_mode';

}

namespace CoMa\WP\Options {

   const DEBUG_SHOW_ELEMENTS = 'debug_show_elements';
   const DEBUG_LIVERELOAD = 'debug_livereload_css';

   const CACHE_PAGE = 'cache_page';
   const CACHE_POST = 'cache_post';
   const CACHE_PATH = 'cache_path';

   const CACHE_MEDIA_PNG_QUALITY = 'cache_media_png_quality';
   const CACHE_MEDIA_JPEG_QUALITY = 'cache_media_jpeg_quality';

   const USE_WP_PAGE_POST_REVISION = 'use_wp_page_post_revision';

   const DB_VERSION = 'db_version';

   const GLOBAL_PROPERTIES = 'global_properties';

}

namespace CoMa\Roles {

   const OPTIONS = 'options';
   const CONTENT_MANAGER = 'content_manager';
   const CACHE = 'cache';
   const CACHE_CLEAR_ATTACHMENTS = 'cache_clear_attachments';
   const WARRANTIES = 'warranties';
   const CONTROLLER_BROWSER = 'controller_browser';
   const RESET_POST_CACHE = 'reset_post_cache';
   const PAGE_PROPERTIES = 'page_properties';
   const PAGE_PROPERTIES_EDIT = 'page_properties_edit';
   const GLOBAL_PROPERTIES = 'global_properties';
   const GLOBAL_PROPERTIES_EDIT = 'global_properties_edit';
   const AREA = 'area';
   const AREA_EDIT = 'area_edit';
   const AREA_REMOVE = 'area_remove';
   const COMPONENT = 'component';
   const COMPONENT_SELECT = 'component_select';
   const COMPONENT_COPY = 'component_copy';
   const COMPONENT_MOVE = 'component_move';
   const COMPONENT_SET_RANK = 'component_set_rank';
   const COMPONENT_RANK_UP = 'component_rank_up';
   const COMPONENT_RANK_DOWN = 'component_rank_down';
   const COMPONENT_EDIT = 'component_edit';
   const COMPONENT_REMOVE = 'component_remove';
   const COMPONENT_DISABLE = 'component_disable';
   const COMPONENT_PROPERTIES_EDIT = 'component_properties_edit';
   const COMPONENT_PROPERTIES_RENAME = 'component_properties_rename';
   const COMPONENT_PROPERTIES_REMOVE = 'component_properties_remove';

}

namespace CoMa\Properties\Page {

   const DISABLE_CACHE = 'disable_cache';
   const RESET_CACHE = 'reset_cache';

}
