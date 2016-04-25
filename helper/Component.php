<?php

namespace CoMa\Helper;

class Component
{

    /**
     * Ruft das angegebene Area ab.
     * @param string $position
     * @param number $parentId
     * @return \CoMa\Base\Area
     */
    public static function getAreaByPositionAndParent($position, $parentId, $class = null)
    {
        global $wpdb;
        $result = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE type_id=' . \CoMa\TYPE_AREA . ' AND parent_id=' . esc_sql($parentId) . ' AND position="' . esc_sql($position) . '" LIMIT 1', ARRAY_A);
        if (!$result) {
            $result = ['position' => $position, 'parentId' => $parentId];
        }
        return self::parseArea($result, $class);
    }

    /**
     * Ruft die Id des angegebenen Area ab.
     * @param string $position
     * @param number $parentId
     * @return number
     */
    public static function getAreaIdByPositionAndParent($position, $parentId)
    {
        global $wpdb;
        $result = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE type_id=' . \CoMa\TYPE_AREA . ' AND parent_id=' . esc_sql($parentId) . ' AND position="' . esc_sql($position) . '" LIMIT 1', ARRAY_A);
        return $result['id'];
    }

    /**
     * Ruft das angegebene Area ab.
     * @param string $position
     * @param number $pageId
     * @return \CoMa\Base\Area
     */
    public static function getAreaByPosition($position, $pageId = null, $class = null)
    {
        global $wpdb;
        if ($pageId == null && !is_numeric($pageId)) {
            $pageId = Base::getPageId();
        }

        $result = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE page_id=' . esc_sql($pageId) . ' AND position="' . esc_sql($position) . '" LIMIT 1', ARRAY_A);

        if (!$result) {
            $result = ['position' => $position, 'page_id' => $pageId];
        }

        return self::parseArea($result, $class);
    }

    /**
     * Ruft das angegebene Area ab.
     * @param number $id
     * @return \CoMa\Base\Area
     */
    public static function getAreaById($id = null)
    {
        global $wpdb;
        return self::parseArea($wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE id=' . esc_sql($id) . ' LIMIT 1', ARRAY_A));
    }

    /**
     * Überprüft ob das mit der Position angegebene Area existiert.
     * @param number $id
     * @return bool
     */
    public static function areaExistByPosition($position)
    {
        global $wpdb;
        return $wpdb->get_var('SELECT COUNT(id) FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE position="' . esc_sql($position) . '" LIMIT 1') > 0;
    }

    /**
     * Überprüft ob das mit der Id angegebene Area existiert.
     * @param $id
     * @return bool
     */
    public static function areaExistById($id)
    {
        global $wpdb;
        return $wpdb->get_var('SELECT COUNT(id) FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE id=' . esc_sql($id) . ' LIMIT 1') > 0;
    }

    /**
     * Ruft den angegebenen Controller ab.
     * @param number $id
     * @return \CoMa\Base\Component
     */
    public static function getComponentById($id = null)
    {
        return self::parseComponent(\CoMa\Helper\Controller::getControllerById($id));
    }

    private static function parseComponent($data)
    {
        $class = 'CoMa\Base\ThemeComponent';
        if ($data['class']) {
            $class = Base::performClassName($data['class']);
        }
        if (!class_exists($class)) {
            $class = 'CoMa\Base\ThemeComponent';
        }
        /**
         * @var \CoMa\Base\Controller $component
         */
        $component = new $class();
        $component->parse($data);
        $component->setClass($class);
        return $component;
    }

    /**
     * @param array $data
     * @param string $class
     * @return \CoMa\Base\ThemeArea
     */
    private static function parseArea($data, $class = null)
    {
        global $CONTENT_MANAGER_PAGE_AREAS;

        if (($data['id'] || $data['parentId']) || ($data['position'] && (is_numeric($data['page_id'])))) {
            if ($data['class']) {
                $class = Base::performClassName($data['class']);
            }
            if (!$class) {
                $class = '\CoMa\Base\ThemeArea';
            }

            if (!class_exists($class)) {
                Base::log('area class [' . $class . '] not exist...');
            } else {
                $area = new $class();

                if ($class) {
                    $data['class'] = $class;
                }
                $area->parse($data);
                $CONTENT_MANAGER_PAGE_AREAS[$data['position']] = $area;
            }
        } else {
            $area = $CONTENT_MANAGER_PAGE_AREAS[$data['position']];
        }
        return $area;
    }


    /**
     * Get next Controller Rank.
     * @param integer $areaId
     * @return integer
     */
    public static function getNextComponentRank($areaId)
    {
        global $wpdb;
        $rank = $wpdb->get_var('SELECT rank FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE parend_id=' . esc_sql($areaId) . ' ORDER BY RAND DESC LIMIT 1', ARRAY_A);
        if (!$rank) {
            $rank = 0;
        }
        return $rank + 1;
    }
}

?>