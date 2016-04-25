<?php

namespace CoMa\Base;

class PageProperties extends PropertyHandler
{

    public $pageId;
    public $exist = false;

    public function __construct()
    {
        global $wpdb;
        $this->sqlTable = $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_PAGES);
    }

    public function load()
    {
        global $wpdb;
        $properties = $wpdb->get_var('SELECT properties FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_PAGES) . ' WHERE page_id=' . esc_sql($this->pageId) . ' LIMIT 1');
        if ($properties) {
            $properties = json_decode(stripslashes($properties), true);
            $this->exist = true;
            return $properties;
        }
        return null;
    }

    public function isExist()
    {
        global $wpdb;
        return $wpdb->get_var('SELECT COUNT(page_id) FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_PAGES) . ' WHERE page_id=' . esc_sql($this->pageId) . ' LIMIT 1');
    }

    public function save()
    {
        global $wpdb;
        $properties = esc_sql(json_encode($this->get()));
        if ($this->isExist()) {
            $wpdb->update(
                $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_PAGES),
                ['properties' => $properties],
                ['page_id' => $this->pageId]
            );
        } else {
            $wpdb->insert($wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_PAGES), [
                'page_id' => $this->pageId,
                'properties' => $properties
            ]);
        }
    }

}

?>