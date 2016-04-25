<?php

namespace CoMa\Base;

class Controller
{

    const TYPE = \CoMa\Helper\Base::TYPE_CONTROLLER;

    const TEMPLATE_NAME = "Controller";
    const TEMPLATE_ID = null;
    const TEMPLATE_PATH = null;

    /**
     * Controls
     */
    const CONTROL_SET_RANK = 'set_rank';
    const CONTROL_RANK_UP = 'rank_up';
    const CONTROL_RANK_DOWN = 'rank_down';
    const CONTROL_EDIT = 'edit';
    const CONTROL_REMOVE = 'remove';
    const CONTROL_DISABLED = 'disabled';
    const CONTROL_APPEND = 'append';
    const CONTROL_MOVE = 'move';
    const CONTROL_COPY = 'copy';

    /**
     * Set max childlren amount. 0 is default.
     * @type number MAX_CHILD_COUNT
     */
    const MAX_CHILD_COUNT = 0;

    private $id = null;
    private $childrens = [];
    private $properties = [];

    private $rank = null;
    private $page_id = null;
    private $parent_id = null;
    private $type_id = null;
    private $class = null;
    private $position = null;
    private $disabled = null;

    /**
     * Active Controls for EditMode.
     * @var array
     */
    private $controls = [
        self::CONTROL_SET_RANK => true, self::CONTROL_RANK_UP => false, self::CONTROL_RANK_DOWN => false,
        self::CONTROL_EDIT => true, self::CONTROL_REMOVE => true, self::CONTROL_DISABLED => true,
        self::CONTROL_APPEND => true, self::CONTROL_MOVE => true, self::CONTROL_COPY => true
    ];

    public static function parseProperties($properties = null)
    {
        if ($properties) {
            foreach ($properties as $key => $property) {
                if (is_array($property)) {
                    $properties[$key] = self::parseProperties($property);
                } else {
                    $properties[$key] = stripslashes(rawurldecode($property));
                }
            }
        }
        return $properties;
    }

    public function getControl($key)
    {
        if (array_key_exists($key, $this->controls)) {
            return $this->controls[$key];
        } else {
            return false;
        }
    }

    public function setControls($controls)
    {
        foreach ($controls as $key => $control) {
            if (array_key_exists($key, $this->controls)) {
                $this->controls[$key] = (boolean)$controls[$key];
            }
        }
    }

    public function __construct($properties = [], $id = null)
    {
        $this->id = $id;
        if (is_string($properties)) {
            $this->properties = json_decode($properties, true);
        } else {
            $this->properties = $properties;
        }
    }

    /**
     * @param Controller $self
     * @return bool
     */
    public static function checkChildCount($self)
    {
        return $self::MAX_CHILD_COUNT == 0 || count($self->getChildrens()) < $self::MAX_CHILD_COUNT;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Get page-id or parent page-id.
     * @param bool|false $fromParent
     * @return number
     */
    public function getPageId($fromParent = false)
    {
        $pageId = $this->page_id;
        if ($fromParent) {
            $parent = $this->getParent();
            if ($parent) {
                return $parent->getPageId(true);
            }
        }
        return $pageId;
    }

    public function setPageId($pageId)
    {
        $this->page_id = $pageId;
    }


    public function getParentId()
    {
        return $this->parent_id;
    }

    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;
    }

    public function getTypeId()
    {
        return $this->type_id;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        return $this->position = $position;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    public function getProperty($name)
    {
//        if (!isset($this->properties[$name]))
//            return null;
        return $this->properties[$name];
    }

    public function getRank()
    {
        return $this->rank;
    }

    public function getDisabled()
    {
        return $this->disabled;
    }

    public function parent_id($parentId)
    {
        $this->parent_id = $parentId;
    }

    public function rank($rank)
    {
        $this->rank = $rank;
    }

    public function disabled($disabled)
    {
        $this->disabled = $disabled;
    }

    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return array
     */
    public function getChildrens()
    {
        return $this->childrens;
    }

    /**
     * @return null|Component
     */
    public function getParent()
    {
        if ($this->parent_id > 0) {
            return \CoMa\Helper\Component::getComponentById($this->parent_id);
        }
        return null;
    }

    public function render()
    {

    }

    public function getTemplatePath()
    {
        return PLUGIN_TEMPLATE_PATH . self::TEMPLATE_PATH;
    }

    /**
     * Removes controller and the children
     */
    public function remove()
    {
        global $wpdb;

        /*
         * Remove Childrens
         */
        foreach ($this->getChildrens() as $controller) {
            /**
             * @type \CoMa\Base\Controller $controller
             */
            $controller->remove();
        }

        $wpdb->delete($wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS), ['id' => $this->id]);
    }


    public function parse($data)
    {
        $this->id = $data['id'];
        $this->page_id = $data['page_id'];
        $this->parent_id = $data['parent_id'];
        $this->type_id = $data['type_id'];
        $this->class = $data['class'];
        $this->position = $data['position'];
        $this->rank = $data['rank'];
        $this->disabled = $data[self::CONTROL_DISABLED];

        $this->properties = self::parseProperties(json_decode(stripslashes($data['properties']), true));

        $this->childrens = self::getLoadChildrens($this, \CoMa\Helper\Base::isEditMode());

    }

    /**
     * Ruft ab ob der Controller exisitert.
     * @return bool
     */
    public function exist()
    {
        return $this->id != null;
    }


    /**
     * @param Controller $controller
     * @param bool $withDisabled
     * @return array
     */
    private static function getLoadChildrens($controller, $withDisabled)
    {
        $id = $controller->getId();
        global $wpdb;
        $controllerData = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE id != ' . $id . ' AND parent_id=' . $id . (!$withDisabled ? ' AND DISABLED=0' : '') . ' ORDER BY rank ASC', ARRAY_A);
        $controllers = [];
        foreach ($controllerData as $data) {
            $class = \CoMa\Helper\Base::performClassName($data['class']);
            if (!class_exists($class)) {
                \CoMa\Helper\Base::log('Controller class [' . $class . '] not exist...');
            } else {
                /**
                 * @type Controller $controller
                 */
                $controller = new $class($data->properties);
                $controller->parent_id($id);
                $controller->parse($data);
                $controllers[] = $controller;
            }
        }
        return $controllers;
    }


    /**
     * Map controller properties.
     * @param $map
     * @param $force
     */
    public function mapProperties($map, $force = false)
    {
        $properties = $this->getProperties();
        $fieldNames = $this->getPropertyDialog()->getAllFieldNames();

        foreach ($fieldNames as $name) {
            if (array_key_exists($name, $map)) {
                $properties[$name] = $map[$name];
            }
        }
        $this->setProperties($properties);
    }

    /**
     * Override for property-dialog.
     * @return PropertyDialog
     */
    public function getPropertyDialog()
    {
        return null;
    }


    /**
     * Get default property-dialog.
     * @return PropertyDialog
     */
    public static function defaultPropertyDialog()
    {
        $propertyDialog = new PropertyDialog();
        $propertyDialog->title('Property Dialog');
        return $propertyDialog;
    }

}

?>
