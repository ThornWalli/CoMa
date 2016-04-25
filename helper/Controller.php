<?php

namespace CoMa\Helper;

class Controller
{

    public static function getAllControllers()
    {
        global $wpdb;
        $result = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS), ARRAY_A);
        return $result;
    }

    /**
     * Ruft die der Seite zugewiesenen Controller aus der Datenbank ab.
     * @param number $pageId
     * @return mixed
     */
    public static function getControllersByPageId($pageId)
    {
        global $wpdb;
        $result = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE page_id=' . $pageId . ' AND parent_id=0 ORDER BY rank ASC;', ARRAY_A);
        return $result;
    }

    /**
     * Ruft die einer zugewiesenen Controller, unter Controller aus der Datenbank ab.
     * @param number $parentId
     * @return mixed
     */
    public static function getControllersByParentId($parentId)
    {
        global $wpdb;
        $result = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE parent_id=' . $parentId . ' ORDER BY rank ASC;', ARRAY_A);
        return $result;
    }

    /**
     * Ruft einen Controller aus der Datenbank ab.
     * @param number $id
     * @return array
     */
    public static function getControllerById($id)
    {
        global $wpdb;
        return $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE id=' . esc_sql($id) . ' LIMIT 1;', ARRAY_A);
    }

    /**
     * Verarbeitet die aus der Datenbank abgefragten Daten und liefert ein Objekt zurück.
     * @param array $data
     * @return \CoMa\Base\Controller
     */
    public static function parseController($data)
    {
        $controller = new \CoMa\Base\Controller();
        $controller->parse($data);
        return $controller;
    }

    /**
     * Löscht den angegebenen Controller mit seinen unter Controllern.
     * @param mixed $controller
     */
    public static function removeController($controller)
    {
        $id = self::getIdFromController($controller);
        $controller = \CoMa\Helper\Component::getComponentById($id);
        /**
         * @type \CoMa\Base\Controller $controller_
         */
        foreach (self::getControllerChildrens($controller) as $controller_) {
            self::removeController($controller_->getId());
        }
        $controller->remove();

    }


    /**
     * Ruft untergeordnete Controller von einem Controller ab.
     * @param \CoMa\Base\Controller $controller
     * @param bool $all Gibt an ob, alle Controller zurück gegeben werden.
     * @return array
     */
    public static function getControllerChildrens($controller, $all = false)
    {
        $childrens = [];
        foreach ($controller->getChildrens() as $children) {
            $childrens[] = $children;
            if ($all) {
                $childrens = array_merge($childrens, self::getControllerChildrens($children, $all));
            }
        }
        return $childrens;
    }

    /**
     * Löscht alle Controller
     */
    public static function deleteAllControllers()
    {
        global $wpdb;
        $wpdb->query('truncate table ' . Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS));
    }

    /**
     * Ruft die Seiten-Id des Controllers ab. Dies geschieht auch über darüber liegende Controller hinaus.
     * @param $controller
     */
    public static function getPageIdFromController($controller)
    {
        $id = self::parseController($controller);

        $controller = self::getControllerById($id);
        $controller->getPageId(true);


    }

    /**
     * Ruft die Anzahl der untergeordneten Controller ab.
     * @param $controller
     * @return int
     */
    public static function getChildrenCount($controller)
    {
        if (!$controller) {
            return 0;
        }
        $id = self::getIdFromController($controller);
        global $wpdb;
        $controllerData = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE id != ' . $id . ' AND parent_id=' . $id . ' ORDER BY rank ASC', ARRAY_A);
        return count($controllerData);
    }

    /**
     * Nimmt den übergebenen Controller oder Id und gibt die Id zurück.
     * @param mixed $controller
     * @return mixed
     */
    private static function getIdFromController($controller)
    {
        if (is_numeric($controller)) {
            return $controller;
        } else {
            return $controller->getId();
        }
    }

    /**
     * Ruft die aktuelle Id des Eltern Controllers zurück.
     * @return int
     */
    public static function getParentId()
    {
        global $CONTENT_MANAGER_PARENT_COMPONENT;
        if (!$CONTENT_MANAGER_PARENT_COMPONENT) {
            return 0;
        }
        return $CONTENT_MANAGER_PARENT_COMPONENT->getId();
    }

    /**
     * @param \CoMa\Base\Controller $controller
     * @param array $ids
     */
    private static function _allControllers($controller, &$controllers)
    {
        $controllers[] = $controller;
        $childrens = $controller->getChildrens();
        foreach ($childrens as $children) {
            self::_allControllers($children, $controllers);
        }
    }

    /**
     * Get all controllers from Page.
     * @param $pageId
     * @return array
     */
    public static function getAllControllersFromPage($pageId)
    {
        $controllers = [];
        foreach (Controller::getControllersByPageId($pageId) as $controllerData) {
            $controller = new \CoMa\Base\Controller();
            $controller->parse($controllerData);
            self::_allControllers($controller, $controllers);
        }
        return $controllers;
    }

    /**
     * @param \CoMa\Base\Controller $controller
     */
    public static function getAllControllersFromController($controller)
    {
        $controllers = [];
        foreach ($controller->getChildrens() as $controller) {
            self::_allControllers($controller, $controllers);
        }
        return $controllers;
    }

    public static function saveController($data)
    {
        global $wpdb;

        $properties = str_replace('\\', '\\\\', $data['properties']);

        $values = [];
        if ($data['rank'])
            $values['rank'] = esc_sql($data['rank']);
        if ($data['page_id'])
            $values['page_id'] = esc_sql($data['page_id']);
        if ($data['parent_id'])
            $values['parent_id'] = esc_sql($data['parent_id']);
        if ($data['type_id'])
            $values['type_id'] = esc_sql($data['type_id']);
        if ($data['class'])
            $values['class'] = esc_sql($data['class']);
        if ($data['properties'])
            $values['properties'] = esc_sql(json_encode($properties));
        if ($data['position'])
            $values['position'] = esc_sql($data['position']);

        if (isset($data['id']) && $data['id']) {
            $wpdb->update(
                $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS),
                $values,
                ['id' => $data['id']]
            );
            return $data['id'];
        } else {
            $wpdb->insert($wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS), $values);
            return $wpdb->insert_id;
        }
    }


    /**
     * @param \CoMa\Base\Controller $controller
     * @param null|\CoMa\Base\Controller $parent
     * @return null|\CoMa\Base\Controller
     */
    public static function cloneController($controller, $parent = null)
    {

        $parentId = $controller->getParentId();
        $class = $controller->getClass();
        if ($parent) {
            $parentId = $parent->getId();
            $class = Base::performClassName($class);
        }

        $id = \CoMa\Helper\Controller::saveController([
            'page_id' => $controller->getPageId(),
            'parent_id' => $parentId,
            'type_id' => $controller->getTypeId(),
            'class' => $class,
            'position' => $controller->getPosition(),
            'rank' => $controller->getRank(),
            'properties' => $controller->getProperties()
        ]);

        $clone = Controller::parseController(Controller::getControllerById($id));

        foreach ($controller->getChildrens() as $children) {
            self::cloneController($children, $clone);
        }

        return $clone;

    }

    public static function removeControllerFromPage($pageId)
    {
        $pageControllers = self::getControllersByPageId($pageId);
        foreach ($pageControllers as $pageControllerData) {
            $controller = \CoMa\Helper\Controller::parseController($pageControllerData['id']);
            $controller->remove();
        }
    }

}

?>