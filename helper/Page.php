<?php

namespace CoMa\Helper;

class Page
{

    public static function saveProperties($properties, $pageId = null)
    {

        if (!$pageId) {
            $pageId = \CoMa\Helper\Base::getPageId();
            \CoMa\Helper\Base::getPageProperties($pageId)->set($properties);
        }

    }

    public static function getProperties($pageId = null)
    {

        if (!$pageId) {
            $pageId = \CoMa\Helper\Base::getPageId();
        }
        global $wpdb;
        $result = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_PAGES) . ' WHERE page_id=' . $pageId . ' LIMIT 1;', ARRAY_A);
        return $result;

    }

    public static function hasProperties($pageId = null)
    {

        if (!$pageId) {
            $pageId = \CoMa\Helper\Base::getPageId();
        }

    }

    public static function deleteProperties($pageId = null)
    {

        if (!$pageId) {
            $pageId = \CoMa\Helper\Base::getPageId();
        }

    }


}