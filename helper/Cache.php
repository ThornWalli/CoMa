<?php

namespace CoMa\Helper;


use CoMa\Base\PropertyDialog;

class Cache
{


    public static function init()
    {

        if (Base::getWPOption(\CoMa\WP\Options\CACHE_MEDIA_PNG_QUALITY)) {
            add_filter('png_quality', function () {
                return Base::getWPOption(\CoMa\WP\Options\CACHE_MEDIA_PNG_QUALITY);
            });
        }
        if (Base::getWPOption(\CoMa\WP\Options\CACHE_MEDIA_JPEG_QUALITY)) {
            add_filter('jpeg_quality', function () {
                return Base::getWPOption(\CoMa\WP\Options\CACHE_MEDIA_JPEG_QUALITY);
            });
        }

        add_filter(\CoMa\WP\Filter\PAGE_PROPERTIES_DIALOG, function ($propertyDialog) {
            /**
             * @type PropertyDialog $propertyDialog
             */
            if ($propertyDialog instanceof PropertyDialog) {
                $tab = $propertyDialog->addTab('cache', __('Cache', \CoMa\PLUGIN_NAME));
                $tab->addCheckBox(\CoMa\Properties\Page\DISABLE_CACHE, 'Disable Cache?')->description('Disable cache on current page/post');
                $tab->addCheckBox(\CoMa\Properties\Page\RESET_CACHE, 'Reset Cache?')->description('Reset cache on current page/post');
            }
            return $propertyDialog;
        });

        if (!is_user_logged_in()) {
            add_action('wp', function ($name) {
                if (self::allowCached()) {
                    $fileName = self::getFile();
                    if (Base::getPageProperty(\CoMa\Properties\Page\RESET_CACHE)) {
                        Base::setPageProperty(\CoMa\Properties\Page\RESET_CACHE, null);
                        if (file_exists($fileName)) unlink($fileName);
                    }
                    if (file_exists($fileName)) {
                        include($fileName);
                        exit();
                    } else {
                        ob_start();
                    }
                }
            });

            add_action('get_footer', function ($name) {
                if (self::allowCached()) {
                    wp_reset_postdata();
                    $fileName = self::getFile();
                    global $CONTENT_MANAGER_FOOTER_CACHE;
                    if (!$CONTENT_MANAGER_FOOTER_CACHE) {
                        $CONTENT_MANAGER_FOOTER_CACHE = true;
                        $html = ob_get_clean();
                        ob_start();
                        get_footer();
                        $html .= ob_get_clean();
                        $CONTENT_MANAGER_FOOTER_CACHE = false;
                        file_put_contents($fileName, $html);
                        echo $html;
                    }
                }
            });

        }

    }


    public static function deleteCache($type)
    {
        Base::cleanCacheDirectory(self::getPath());
        return;
    }

    public static function allowCached($id = null)
    {
        if (is_page($id)) {
            if (!Base::getWPOption(\CoMa\WP\Options\CACHE_PAGE)) {
                return false;
            }
        } else if (!Base::getWPOption(\CoMa\WP\Options\CACHE_PAGE)) {
            return false;
        }
        if (!$id) {
            $id = Base::getPageId();
        }
        return !Base::getPageProperty(\CoMa\Properties\Page\DISABLE_CACHE, $id);
    }

    public static function getFile($id = null)
    {
        $type = get_post_type($id);
        return self::getPath() . '/' . ($type == 'page' ? 'page' : 'post') . '_' . ($id == null ? get_the_ID() : $id);
    }

    public static function isCached($pageId)
    {
        return self::allowCached() && file_exists(self::getFile($pageId));
    }

    private static function getPath()
    {
        $path = Base::getWPOption(\CoMa\WP\Options\CACHE_PATH);
        if (empty($path)) {
            $path = \CoMa\DEFAULT_CACHE_PATH;
        } else {
            if (preg_match('/PLUGIN_PATH|THEME_PATH/', $path)) {
                $path = str_replace('%PLUGIN_PATH%', \CoMa\PLUGIN_PATH, $path);
                $path = str_replace('%THEME_PATH%', \CoMa\THEME_PATH, $path);
            } else {
                $path = str_replace('\\', '/', getcwd()) . '/' . $path;
            }
        }
        return $path;
    }

}

?>