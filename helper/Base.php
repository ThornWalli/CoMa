<?php

namespace CoMa\Helper;

class Base
{

   private static $isEditMode = false;
   const TYPE_CONTROLLER = 'controller';
   const TYPE_COMPONENT = 'component';
   const TYPE_AREA = 'area';

   /**
    * Content-Manager Berechtigungen.
    * @var array
    */
   public static $ROLE_CAPS = [

      \CoMa\Roles\CONTENT_MANAGER,

      \CoMa\Roles\CACHE,
      \CoMa\Roles\CACHE_CLEAR_ATTACHMENTS,
      \CoMa\Roles\WARRANTIES,
      \CoMa\Roles\CONTROLLER_BROWSER,

      \CoMa\Roles\RESET_POST_CACHE,

      \CoMa\Roles\PAGE_PROPERTIES,
      \CoMa\Roles\PAGE_PROPERTIES_EDIT,
      \CoMa\Roles\GLOBAL_PROPERTIES,
      \CoMa\Roles\GLOBAL_PROPERTIES_EDIT,

      \CoMa\Roles\AREA,
      \CoMa\Roles\AREA_EDIT,
      \CoMa\Roles\AREA_REMOVE,

      \CoMa\Roles\COMPONENT,
      \CoMa\Roles\COMPONENT_SELECT,
      \CoMa\Roles\COMPONENT_COPY,
      \CoMa\Roles\COMPONENT_MOVE,
      \CoMa\Roles\COMPONENT_SET_RANK,
      \CoMa\Roles\COMPONENT_RANK_UP,
      \CoMa\Roles\COMPONENT_RANK_DOWN,
      \CoMa\Roles\COMPONENT_EDIT,
      \CoMa\Roles\COMPONENT_REMOVE,
      \CoMa\Roles\COMPONENT_DISABLE,

      \CoMa\Roles\COMPONENT_PROPERTIES_EDIT,
      \CoMa\Roles\COMPONENT_PROPERTIES_RENAME,
      \CoMa\Roles\COMPONENT_PROPERTIES_REMOVE,


      \CoMa\Roles\GLOBAL_PROPERTIES_EDIT,
      \CoMa\Roles\OPTIONS

   ];

   /**
    * Ruft die Rolle des Benutzers ab.
    * @return string
    */
   public static function getCurrentUserRole()
   {
      global $current_user;
      get_currentuserinfo();
      $user_roles = $current_user->roles;
      $user_role = array_shift($user_roles);
      return $user_role;
   }

   /**
    * Ruft alle editierbaren Rollen ab.
    * @return mixed
    */
   public static function getEditableRoles()
   {
      global $wp_roles;
      $all_roles = $wp_roles->roles;
      return apply_filters('editable_roles', $all_roles);
   }

   /**
    * Ruft alle Berechtigungen ab.
    * @return array
    */
   public static function getAllCapabilities()
   {
      $capabilities = [];
      foreach (self::getEditableRoles() as $role) {
         $capabilities = array_merge($capabilities, $role['capabilities']);
      }
      return $capabilities;
   }

   /**
    * Ruft ab, ob der Benutzer die angegebene Berechtigung hat.
    * @param $cap
    * @return boolean
    */
   public static function roleHasCap($cap)
   {
      if (is_array($cap)) {
         foreach ($cap as $c) {
            if (!self::getPrefixedName($c)) {
               return false;
            }
         }
      }
      return current_user_can(self::getCapName($cap));
   }

   public static function getCapName($cap)
   {
      return self::getPrefixedName($cap);
   }

   /**
    * Ruft ab, ob der Benutezr ein Administrator ist.
    * @return boolean
    */
   public static function isAdministrator()
   {
      return self::getCurrentUserRole() == 'administrator';
   }


   private static $globalProperties;

   /**
    * Ruft die Globalen-Eigenschaften ab.
    * @return \CoMa\Base\GlobalProperties
    */
   public static function getGlobalProperties()
   {
      if (empty(self::$globalProperties)) {
         self::$globalProperties = new \CoMa\Base\GlobalProperties();
      }
      return self::$globalProperties;
   }

   private static $globalPropertyTypes;

   public static function getGlobalPropertyTypes()
   {
      if (empty(self::$globalPropertyTypes)) {
         $propertyDialog = new \CoMa\Base\PropertyDialog();
         $propertyDialog->title(__('Global Properties', \CoMa\PLUGIN_NAME));
         $propertyDialog = apply_filters(\CoMa\WP\Filter\GLOBAL_PROPERTIES_DIALOG, $propertyDialog);
         self::$globalPropertyTypes = $propertyDialog->getAllFieldTypes();
      }
      return self::$globalPropertyTypes;
   }

   private static $pagePropertiesList = [];

   /**
    * Ruft die Seiten-Eigenschaften ab.
    * @param number $pageId
    * @return \CoMa\Base\PageProperties
    */
   public static function getPageProperties($pageId = null)
   {
      if (!$pageId) {
         $pageId = Base::getPageId();
      }
      if (!array_key_exists($pageId, self::$pagePropertiesList)) {
         self::$pagePropertiesList[$pageId] = new  \CoMa\Base\PageProperties();
         self::$pagePropertiesList[$pageId]->pageId = $pageId;
      }
      return self::$pagePropertiesList[$pageId];
   }

   private static $logs = [];

   /**
    * Fügt ein Log-Eintrag hinzu.
    * @param string $log
    * @param string $type
    */
   public static function log($log, $type = \CoMa\WP_ADMIN_NOTICE_TYPE_ERROR)
   {
      if (!in_array($log, self::$logs)) {
         self::$logs[$log] = ['text' => $log, 'type' => $type];
      }
   }

   /**
    * Ruft alle Log-Einträge ab.
    * @return array
    */
   public static function getLogs()
   {
      return self::$logs;
   }

   /**
    * Rendert WP-Admin-Notice.
    * @param $text
    * @param string $type
    */
   public static function renderAdminNotice($text, $type = \CoMa\WP_ADMIN_NOTICE_TYPE_INFO)
   {
      ?>
      <div class="<?php echo $type; ?> notice is-dismissible">
         <p>
            <?php echo $text; ?>
         </p>
      </div>
      <?php
   }

   /**
    * Löscht alle generierten Dateien im Upload-Ordner.
    * Es betrifft nicht die Quelldateien.
    * @param string $path
    */
   public static function cleanUploadDirectory($path)
   {
      function removeDir($dir)
      {
         if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
               if ($file != "." && $file != "..") {
                  if (filetype($dir . "/" . $file) == "dir") {
                     removeDir($dir . "/" . $file);
                  } else {
                     $matches = [];
                     preg_match_all("/(.*)[-](\\d+[x]\\d+)\\..+/", $file, $matches);
                     if ($matches[2]) {
                        unlink($dir . "/" . $file);
                     }
                  }
               }
            }
            @rmdir($dir);
         } else {
            return 'doesn\'t exist or inaccessible!';
         }
      }

      removeDir($path);
   }

   public static function removePostCache($postId)
   {
      unlink(Cache::getFile($postId));
   }

   /**
    * Löscht alle generierten Dateien im Cache-Ordner.
    * @param string $path
    * @param string|null $type
    */
   public static function cleanCacheDirectory($path, $type = null)
   {
      if (is_dir($path)) {
         $files = scandir($path);
         foreach ($files as $file) {
            if ($file != "." && $file != ".." && $file != ".keep") {
               if ($type == 'page' && strpos('page_', $file) > -1 || $type == 'post' && strpos('post_', $file) > -1 || $type == null) {
                  unlink($path . "/" . $file);
               }

            }
         }
      } else {
         return 'doesn\'t exist or inaccessible!';
      }
   }

   /**
    * Bereitet Text-Inhalt aus dem Editor zum rendern vor.
    * @param string $copy
    * @param array $options
    * @return string
    */
   public static function performContent($copy, $options = [])
   {
      $br = true;
      if (is_array($options)) {
         /**
          * $options['br']
          * Optional. If set, this will convert all remaining line-breaks
          * after paragraphing. Default true.
          */
         if (isset($options['br']))
            $br = $options['br'];
      }

      if (isset($options['more']) && $options['more'] && preg_match('/<!--more(.*?)?-->/', $copy, $matches)) {
         $matches = explode($matches[0], $copy, 2);
         if (!empty($matches[1])) {
            $copy = strip_tags(trim($matches[0]));
         }
      }

      return stripslashes(apply_filters('the_content', wpautop($copy, $br)));
   }

   public static function performClassName($className)
   {
      return stripslashes($className);
   }

   /**
    * Gibt ein dem Plugin zugeordnete Option zuück.
    * @param string $name
    * @return mixed
    */
   public static function getWPOption($name)
   {
      return get_option(self::getPrefixedSQLName($name));

   }

   /**
    * Legt eine dem Plugin zugeordnete Option fest.
    * @param string $name
    * @param mixed $value
    * @return boolean
    */
   public static function setWPOption($name, $value)
   {

      if (self::getWPOption($name) !== false) {
         return update_option(self::getPrefixedSQLName($name), $value);
      } else {
         return add_option(self::getPrefixedSQLName($name), $value);
      }
   }

   /**
    * Ruft den geprefixed Namen ab.
    * @param string $name
    * @return string
    */
   public static function getPrefixedName($name, $separator = '_')
   {
      return \CoMa\PREFIX . $separator . $name;
   }

   public static function addPreviewGetArg($url, $preview = null)
   {
      return add_query_arg(\CoMa\PREFIX . '-mode', (\CoMa\Helper\Base::isEditMode() && is_null($preview) || is_bool($preview) && !$preview ? 'author' : 'preview'), $url);
   }

   /**
    * Rendert Tag-Attribute mit angegebenen Prefix.
    * @param array $attributes
    * @param string $prefix
    * @return string
    */
   public static function renderTagAttributes($attributes, $prefix = null)
   {

      if ($prefix != null) {
         $prefix = $prefix . '-';
      }
      $tagAttributes = [];
      foreach ($attributes as $name => $attribute) {
         if (!empty($attribute) || is_numeric($attribute)) {

            if (is_bool($attribute) && $attribute == true) {
               $tagAttributes[] = $name;
            } else {
               $tagAttributes[] = $prefix . $name . '="' . $attribute . '"';
            }


         }
      }
      return ' ' . implode(' ', $tagAttributes);

   }

   public static function getSession($name)
   {

      return $_SESSION[\CoMa\SESSION_PREFIX][$name];
   }

   public static function setSession($name, $value)
   {
      $_SESSION[\CoMa\SESSION_PREFIX][$name] = $value;
   }

   public static function postIsset($name)
   {
      return isset($_POST[\CoMa\PREFIX . '-' . $name]);
   }

   public static function getIsset($name)
   {
      return isset($_GET[\CoMa\PREFIX . '-' . $name]);
   }

   /**
    * Gibt POST-Daten mit ungeprefixten Argument zurück.
    * @param string $name
    * @return mixed
    */
   public static function POST($name)
   {
      if (self::postIsset($name)) {
         return $_POST[\CoMa\PREFIX . '-' . $name];
      }
      return null;
   }

   /**
    * Gibt GET-Daten mit ungeprefixten Argument zurück.
    * @param string $name
    * @return mixed
    */
   public static function GET($name)
   {
      if (self::getIsset($name)) {
         return $_GET[\CoMa\PREFIX . '-' . $name];
      }
      return null;
   }


   /**
    * Ruft Bereiche unter der angegebenen Seiten-Id ab.
    * @param number $pageId
    * @return array
    */
   public static function getAreasByPage($pageId = null)
   {
      global $wpdb;
      $controllerData = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . \CoMa\Helper\Base::getPrefixedSQLName(\CoMa\SQL_TABLE_NAME_CONTROLLERS) . ' WHERE parent_id="" AND page_id=' . $pageId, ARRAY_A);
      $controllers = [];
      foreach ($controllerData as $data) {
         $class = Base::performClassName($data['class']);
         if (!$class) {
            $class = 'CoMa\Base\ThemeArea';
         }
         /**
          * @type Controller $controller
          */
         $controller = new $class($data['properties']);
         $controller->parse($data);
         $controllers[$controller->getPosition()] = $controller;

      }
      return $controllers;
   }

   /**
    * Ruft den Bereich ab, der an der angegebenen Position und Klasse liegt.
    * Wenn static aktiv, wird die Seiten-Id ignoriert und der Bereich ist überall vorhanden,
    * wo dieser mit Position und Klasse angegeben ist.
    * @param string $position
    * @param string $class
    * @param boolean $static Gibt an ob das Area statisch ist, wenn ja dann sind die Komponenten auf jeder Seite sichtbar.
    * @return string
    */
   public static function getArea($position, $class = null, $static = false)
   {
      $parentId = Controller::getParentId();
      if ($parentId) {
         $area = Component::getAreaByPositionAndParent($position, $parentId, $class);
         if ($area) {
            $area->parent_id($parentId);
         } else {
            return false;
         }
      } else {

         if ($static) {
            $pageId = 0;
         } else {
            $pageId = self::getPageId();
         }

         $area = Component::getAreaByPosition($position, $pageId, $class);

         if (!$area) {
            Base::log('Can\'t find area');
            return;
         } else {
            $area->setClass($class);
         }
      }
      return $area->render(['echo' => true]);
   }


   /**
    * @param number $pageId
    * @param string $position
    * @param mixed $class
    * @param boolean $class
    * @return string
    */
   public static function getAreaByParent($parent, $position, $class = null, $static = false)
   {
      global $CONTENT_MANAGER_PARENT_COMPONENT;
      $tmpParent = $CONTENT_MANAGER_PARENT_COMPONENT;
      $CONTENT_MANAGER_PARENT_COMPONENT = $parent;
      $area = self::getArea($position, $class, $static);
      $CONTENT_MANAGER_PARENT_COMPONENT = $tmpParent;
      return $area;
   }

   /**
    * @param number $pageId
    * @param string $position
    * @param mixed $class
    * @return string
    */
   public static function getAreaByPage($pageId, $position, $class = null)
   {
      $area = Component::getAreaByPosition($position, $pageId, $class);
      if (!$area) {
         Base::log('Can\'t find area');
         return '';
      } else {
         $area->setClass($class);
         return $area->render(['echo' => true]);
      }
   }

   /**
    * Ruft ab ob der Benutzer sich im Autoren-Modus befindet.
    * @return boolean
    */
   public static function isEditMode()
   {
      return self::$isEditMode;
   }

   /**
    * Ruft ab ob der Benutzer sich im Vorschau-Modus befindet.
    * @return boolean
    */
   public static function isPreview()
   {
      return !self::$isEditMode;
   }

   public static function setupSession()
   {
      if (!session_id()) {
         session_start();
      }
      if (!isset($_SESSION[\CoMa\SESSION_PREFIX]['area-id'])) {
         Base::setSession('area-id', null); // Ausgewählte Area
      }
      if (!isset($_SESSION[\CoMa\SESSION_PREFIX]['selected-component'])) {
         Base::setSession('selected-component', null); // Ausgewählte Komponente
      }
      if (!isset($_SESSION[\CoMa\SESSION_PREFIX]['page-id'])) {
         Base::setSession('page-id', null);
      }
      if (!isset($_SESSION[\CoMa\SESSION_PREFIX]['preview'])) {
         Base::setSession('preview', true);
      }
   }

   /**
    * Ruft die aktuelle Seite-Id ab.
    * @return number
    */
   public static function getPageId()
   {
      global $wp_query;
      $page = Base::getSession('page-id');
      if ($page != $wp_query->post->ID) {
         $page = $wp_query->post->ID;
      }
      return $page;
   }


   public static function getEditableGlobalProperty($name, $echo = false)
   {
      $value = self::getGlobalProperty($name);
      if (Base::isEditMode() && Base::roleHasCap(\CoMa\Roles\GLOBAL_PROPERTIES_EDIT)) {
         $types = Base::getGlobalPropertyTypes();

         global $CONTENT_MANAGER_EDITOR_DISABLE;
         $CONTENT_MANAGER_EDITOR_DISABLE = true;

         if (class_exists($types[$name])) {
            $field = new $types[$name]($name);
         } else {
            $field = new \CoMa\Base\PropertyDialog\Field\TextField($name);
         }
         $field->cssClass('input');
         $field->value($value);
         $html = '<div class="coma-controller partial' . (!$value ? ' empty' : '') . '" data-coma-controller="components/EditableProperty" data-partial="coma/component/editable-property" data-target=".coma-controller[data-coma-controller=\'CoMa\']" data-action="global-edit-property" data-name="' . $name . '" data-value="' . $value . '" lang-empty="' . __('Empty', \CoMa\PLUGIN_NAME) . '">';
         $html .= '<div class="helper"><div class="input">' . $field->getNode() . '</div><a class="save"></a><a class="cancel"></a></div>';
         $html .= '<div class="value">' . ($value ? $value : __('Empty')) . '</div></div>';
         $CONTENT_MANAGER_EDITOR_DISABLE = false;
         if ($echo) {
            echo $html;
            return $value;
         }

         return $html;
      }
      return $value;
   }

   /**
    * Ruft die angegebene Globale-Eigenschaft ab.
    * @param string $name
    * @return mixed
    */
   public static function getGlobalProperty($name, $tab = Template::TAB_DEFAULT)
   {
      $properties = Base::getGlobalProperties()->get();
      if (isset($properties[$tab][$name])) {
         return $properties[$tab][$name];
      }
      return null;
   }

   /**
    * Ruft die angegebene Seiten-Eigenschaft ab.
    * @param string $name
    * @param number $pageId
    * @return mixed
    */
   public static function getPageProperty($name, $tab = Template::TAB_DEFAULT, $pageId = null)
   {
      $properties = Base::getPageProperties($pageId)->get();
      if (isset($properties[$tab][$name])) {
         return $properties[$tab][$name];
      }
      return null;
   }


   /**
    * Legt die angegebene Seiten-Eigenschaft fest.
    * @param string $name
    * @param mixed $value
    * @param number $pageId
    * @return mixed
    */
   public static function setPageProperty($name, $value, $tab = Template::TAB_DEFAULT, $pageId = null)
   {
      $pageProperties = Base::getPageProperties($pageId);
      $properties = $pageProperties->get();
      $properties[$tab][$name] = $value;
      $pageProperties->set($properties);
   }


   /**
    * Ruft die angegebene Seiten-Eigenschaft ab, wenn diese leer, Globale-Eigenschaft.
    * @param string $name
    * @param number $pageId
    * @return mixed
    */
   public static function getProperty($name, $tab = Template::TAB_DEFAULT, $pageId = null)
   {
      if (Base::hasPageProperty($pageId, $tab, $name)) {
         $properties = Base::getPageProperties($pageId)->get();
         if (!empty($properties[$tab][$name])) {
            return Base::getPageProperties($pageId)->get()[$tab][$name];
         }
      }
      return self::getGlobalProperty($name, $tab);
   }

   public static function setEditMode($editMode)
   {
      self::$isEditMode = $editMode;
   }

   private static function hasPageProperty($pageId, $name, $tab = Template::TAB_DEFAULT)
   {
      $Properties = Base::getPageProperties($pageId)->get();
      if (array_key_exists($tab, $Properties) && array_key_exists($name, $Properties[$tab])) {
         return true;
      } else {
         return false;
      }
   }

   public static function getPrefixedSQLName($value, $separator = '_')
   {
      return \CoMa\SQL_PREFIX . $separator . $value;
   }


   /**
    * Ruft alle Attachments mit dem angebenen Typen ab.
    * @param $type
    * @return array
    */
   public static function getAttachments($type = null)
   {
      $attachmentConfig = [
         'post_type' => 'attachment',
         'post_status' => 'inherit',
         'posts_per_page' => -1
      ];
      if ($type) {
         $attachmentConfig['post_mime_type'] = $type;
      }
      $attachmentQuery = new \WP_Query($attachmentConfig);
      $attachments = [];
      foreach ($attachmentQuery->posts as $attachment) {
         $attachments[] = $attachment;
      }
      return $attachments;
   }

   public static function encodeFormDialogProperties($properties)
   {
      foreach ($properties as $key => $property) {
         if (is_array($property)) {
            $properties[$key] = self::encodeFormDialogProperties($property);
         } else {
            $properties[$key] = rawurlencode($property);
         }
      }
      return $properties;
   }

   public static function decodeFormDialogProperties($properties)
   {
      foreach ($properties as $key => $property) {
         if (is_array($property)) {
            $properties[$key] = self::decodeFormDialogProperties($property);
         } else {
            $properties[$key] = rawurldecode($property);
         }
      }
      return $properties;
   }

}


?>
