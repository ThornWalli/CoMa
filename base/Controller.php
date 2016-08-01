<?php

namespace CoMa\Base;

use CoMa\Helper\Base;
use CoMa\Helper\Template;

class Controller
{

   const TYPE = \CoMa\Helper\Base::TYPE_CONTROLLER;

   const TEMPLATE_NAME = "Controller";
   const TEMPLATE_ID = null;
   const TEMPLATE_PATH = null;

   const TAB_DEFAULT = 'default';
   const TAB_CONTENT = 'content';

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

   public function getFlatProperties()
   {

      $properties = [];
      if (isset($this->properties[self::TAB_DEFAULT])) {
         $properties = array_merge($properties, $this->properties[self::TAB_DEFAULT]);
      }
      if (isset($this->properties[self::TAB_CONTENT])) {
         $properties = array_merge($properties, $this->properties[self::TAB_CONTENT]);
      }

      $name = explode('/', $this::TEMPLATE_PATH);
      $name = end($name);
      if (is_array($this->properties)) {
         foreach ($this->properties as $key => $property) {
            if ($key != 'default' && $key != 'content' && $key != $name) {
               $properties[$key] = $property;
            }
         }
      }

      return $properties;
   }

   public function getFlatPropertiesExtended()
   {


      $groupProperties = $this->getGroupProperties();
      $propertyDialog = $this->getPropertyDialog();
      $paths = [];

      foreach ($groupProperties as $groupName => $groupData) {
         if (array_key_exists($groupName, $paths) && !is_array($paths[$groupName])) {
            $paths[$groupName] = [];
         }
         foreach ($groupData as $name => $data) {
            if ($tab = $propertyDialog->getTab($groupName)) {
               $field = $tab->getField($name);
               if ($field) {
                  $groupProperties[$groupName][$name] = $field->compileProperties($data, $groupData);
               } else if ($field = $tab->getField($groupName)) {
                  $groupProperties[$groupName] = $field->compileProperties($groupProperties[$groupName], $groupProperties);
               } else {
                  // fired when property has no property
               }
            }
         }
      }

      $properties = [Template::TAB_DEFAULT => [], Template::TAB_CONTENT => [], Template::TAB_SETTINGS => []];
      if (isset($groupProperties[self::TAB_DEFAULT])) {
         $properties = array_merge($properties, $groupProperties[self::TAB_DEFAULT]);
      }
      if (isset($groupProperties[self::TAB_CONTENT])) {
         $properties = array_merge($properties, $groupProperties[self::TAB_CONTENT]);
      }

      $name = explode('/', $this::TEMPLATE_PATH);
      $name = end($name);
      foreach ($groupProperties as $key => $property) {
         if ($key != 'default' && $key != 'content' && $key != $name) {
            $properties[$key] = $property;
         }
      }

      return $properties;
   }

   public function getGroupProperties()
   {

      $properties = [];
//    if (isset($this->properties[self::TAB_DEFAULT])) {
//      $properties = array_merge($properties, $this->properties[self::TAB_DEFAULT]);
//    }
//    if (isset($this->properties[self::TAB_CONTENT])) {
//      $properties = array_merge($properties, $this->properties[self::TAB_CONTENT]);
//    }

      if (is_array($this->properties)) {
         foreach ($this->properties as $key => $property) {
            $properties[$key] = $property;
         }
      }
      return $properties;
   }


   public function setProperties($properties)
   {
      $this->properties = $properties;
   }

   public function getProperty($name)
   {
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
    * @return Controller[]
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

   public function render($options = null)
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
      if (isset($data['id']))
         $this->id = $data['id'];
      if (isset($data['page_id']))
         $this->page_id = $data['page_id'];
      if (isset($data['parent_id']))
         $this->parent_id = $data['parent_id'];
      if (isset($data['type_id']))
         $this->type_id = $data['type_id'];
      if (isset($data['class']))
         $this->class = $data['class'];
      if (isset($data['position']))
         $this->position = $data['position'];
      if (isset($data['rank']))
         $this->rank = $data['rank'];
      if (isset($data[self::CONTROL_DISABLED]))
         $this->disabled = $data[self::CONTROL_DISABLED];
      if (isset($data['properties']))
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
            $controller = new $class($data['properties']);
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
   public function mapProperties($data, $override = true)
   {
//      $properties = $this->getProperties();
      $properties = $this->getFlatPropertiesExtended();
      foreach ($data as $groupKey => $group) {
         if (!is_array($group)) {
            if ($override && array_key_exists(Template::TAB_DEFAULT, $properties)) {
               $properties[Template::TAB_DEFAULT][$groupKey] = $group;
            }
         } else {
            foreach ($group as $propertyKey => $property) {
               if ($override && array_key_exists($groupKey, $properties)) {
                  $properties[$groupKey][$propertyKey] = $property;
               }
            }
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
      $propertyDialog->addTab(Template::TAB_CONTENT, __('tab_' . Template::TAB_CONTENT, \CoMa\PLUGIN_NAME));
      $propertyDialog->addTab(Template::TAB_SETTINGS, __('tab_' . Template::TAB_SETTINGS, \CoMa\PLUGIN_NAME));
      return $propertyDialog;
   }

}

?>
