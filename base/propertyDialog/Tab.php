<?php

namespace CoMa\Base\PropertyDialog;

use CoMa\Base\PropertyDialog\Field\CodeEditor;
use CoMa\Base\PropertyDialog\Field\Link;
use CoMa\Helper\Template;

class Tab
{

   private $title;
   private $name;

   /**
    * @var Field[]
    */
   private $fields;

   public function __construct($name, $title, $fields = [])
   {
      $this->name = $name;
      $this->title = $title;
      $this->fields = $fields;
   }

   /**
    * Rendert den Tab mit den angebenen Eigenschaften.
    * @param array $properties
    */
   public function render($properties = [])
   {
      include(\CoMa\PLUGIN_TEMPLATE_PATH . 'property-dialog/tab.php');
   }

   /*
    * ##################################################
    * ##################################################
    */

   /**
    * Ruft den Name ab.
    * @return string
    */
   public function getName()
   {
      return $this->name;
   }

   /**
    * Ruft den Titel ab.
    * @return string
    */
   public function getTitle()
   {
      return $this->title;
   }

   /**
    * Gibt alle Felder vom Tab zurück.
    * @return array
    */
   public function getFields()
   {
      return $this->fields;
   }


   /**
    * Ruft alle Feldnamen vom Tab ab.
    * @return array
    */
   public function getFieldNames()
   {
      $names = [];
      foreach ($this->fields as $field) {
         /**
          * @type Field $field
          */
         if ($field instanceof \CoMa\Base\PropertyDialog\Field\Link) {
            $name = $field->getName();
            if (is_array($name)) {
               $name = end($name);
            }
            $names = array_merge($names, Link::getLinkProperties($name));
         } else if ($field instanceof \CoMa\Base\PropertyDialog\Field\CodeEditor) {
            $names = array_merge($names, \CoMa\Helper\Property::getCodeEditorProperties($field->getName()));
         } else {
            $names[] = $field->getName();
         }
      }
      return $names;
   }

   /**
    * Ruft alle Feldtypen vom Tab ab.
    * @return array
    */
   public function getFieldTypes()
   {
      $names = [];
      foreach ($this->fields as $field) {
         /**
          * @type Field $field
          */
         $names[$field->getName()] = get_class($field);
      }
      return $names;
   }

   /*
    * ##################################################
    */

   /**
    * Legt den Namen fest.
    * @param string $name
    * @return Tab
    */
   public function name($name)
   {
      if ($name != null) {
         $this->name = $name;
      }
      return $this;
   }

   /**
    * Legt den Titel fest.
    * @param string $title
    * @return Tab
    */
   public function title($title)
   {
      if ($title != null) {
         $this->title = $title;
      }
      return $this;
   }

   /*
    * ##################################################
    * ##################################################
    */

   /**
    * @param string $name
    * @param Field $field
    * @return mixed
    */
   public function addField($name, $field)
   {
      $field = \CoMa\Helper\Template::getRegisteredCustomField($name);
      $arguments = func_get_args();
      array_shift($arguments);
      $field = new $field(...$arguments);
      $field->tab($this);
      $this->fields[] = $field;
      return $this;
   }

   /**
    * @param Field[] $fields
    * Fügt Felder hinzu.
    * @return $this
    */
   public function addFields($fields)
   {
      foreach ($fields as $field) {
         $field->tab($this);
         $this->fields[] = $field;
      }
//        array_push($this->fields, $fields);
      return $this;
   }

   /**
    * Erzeugt ein Feld.
    * @return Field
    */
   public static function createField()
   {
      return new Field();
   }

   /**
    * Fügt ein Multi-Value Feld hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|array $fields
    * @param null|string $value
    * @param null|array $options
    * @return Field\MultipleValues
    */
   public function addMultiValueField($name, $title = null, $fields = null, $value = null, $options = null)
   {
      $field = new Field\MultipleValues($name, $title, $fields, $value, $options);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Fügt ein Mediathek Feld für Bilder hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|integer $mediaId
    * @param null|array $options
    * @return Field\MediaSelect
    */
   public function addMediaImageSelectField($name, $title = null, $mediaId = null, $options = null)
   {
      $field = self::addMediaSelectField($name, $title, $mediaId, $options);
      $field->mediaType('image');
      return $field;
   }

   /**
    * Fügt ein Mediathek Feld hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|string $mediaType
    * @param null|integer $mediaId
    * @param null|array $options
    * @return Field\MediaSelect
    */
   public function addMediaSelectField($name, $title = null, $mediaId = null, $options = null)
   {
      $field = new Field\MediaSelect($name, $title, $mediaId, $options);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Fügt ein Textfeld hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|string $value
    * @param null|array $options
    * @return Field\TextField
    */
   public function addTextField($name, $title = null, $value = null, $options = null)
   {
      $field = new Field\TextField($name, $title, $value, $options);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Fügt eine Checkbox hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|string $value
    * @param null|array $options
    * @return Field
    */
   public function addCheckbox($name, $title = null, $value = null, $options = null)
   {
      $field = new Field\CheckBox($name, $title, $value, $options);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Fügt eine Radiobox hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|string $value
    * @param null|array $options
    * @return Field\CheckBox
    */
   public function addRadioBox($name, $title = null, $value = null, $options = null)
   {
      $field = new Field\CheckBox($name, $title, $value, $options);
      $field->isRadio(true);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Fügt ein mehrzeiliges Textfeld hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|string $value
    * @param null|array $options
    * @return Field\TextArea
    */
   public function addTextArea($name, $title = null, $value = null, $options = null)
   {
      $field = new Field\TextArea($name, $title, $value, $options);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Fügt ein Editor hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|string $value
    * @param null|array $options
    * @return Field\Editor
    */
   public function addEditor($name, $title = null, $value = null, $options = null)
   {
      $field = new Field\Editor($name, $title, $value, $options);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Fügt ein Editor hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|string $value
    * @param null|array $options
    * @return Field\CodeEditor
    */
   public function addCodeEditor($name, $title = null, $value = null, $options = null)
   {
      $field = new Field\CheckBox($name . CodeEditor::PROPERTY_CODE_LINE_NUMBERS, $title . ' ' . __('Line Numbers', \CoMa\PLUGIN_NAME), $value, $options);
      $this->addFields([$field]);;
      $field = new Field\CodeEditor($name, $title, $value, $options);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Fügt ein DropDown hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|array $items
    * @param null|string $value
    * @param null|array $options
    * @return Field\DropDown
    */
   public function addDropDown($name, $title = null, $items = null, $value = null, $options = null)
   {
      $field = new Field\DropDown($name, $title, $items, $value, $options);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Fügt ein Kategorieauswahl-DropDown hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|string $value
    * @return Field\CategorySelect
    */
   public function addCategorySelect($name, $title = null, $value = null)
   {
      $field = new Field\CategorySelect($name, $title, $value);
      $field->size(10);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Fügt ein Datumauswahl-DropDown hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|string $value
    * @return Field\DateSelect
    */
   public function addDateSelect($name, $title = null, $value = null)
   {
      $field = new Field\DateSelect($name, $title, $value);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Fügt ein DropDown hinzu, zum auswählen von Wp-Menu-Positionen.
    * @param string $name
    * @param null|string $title
    * @param null|string $value
    * @return Field\MenuPositionSelect
    */
   public function addMenuPositionSelect($name, $title = null, $value = null)
   {
      $field = new Field\MenuPositionSelect($name, $title, $value);
      $this->addFields([$field]);;
      return $field;
   }


   /**
    * Fügt ein Multi-Beitragsauswahl-DropDown hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|string $value
    * @return Field\PostSelect
    */
   public function addPostsSelect($name, $title = null, $value = null)
   {
      $field = self::addPostSelect($name, $title, $value);
      $field->options(['multiple' => true])->size(10);
      return $field;
   }

   /**
    * Fügt ein Beitragsauswahl-DropDown hinzu.
    * @param string $name
    * @param null|string $title
    * @param null|string $value
    * @return Field\PostSelect
    */
   public function addPostSelect($name, $title = null, $value = null)
   {
      $field = new Field\PostSelect($name, $title, $value);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Fügt einen Link hinzu.
    * @param string $name
    * @param null|string $title
    * @return Field\Link
    */
   public function addLink($name, $title)
   {
      $field = new Field\Link($name, $title);
      $this->addFields([$field]);
      return $field;
   }

   /**
    * Fügt eine Farbauswahl hinzu.
    * @param string $name
    * @param null|string $title
    * @return Field\ColorPicker
    */
   public function addColorPicker($name, $title)
   {
      $field = new Field\ColorPicker($name, $title);
      $this->addFields([$field]);;
      return $field;
   }


   /**
    * Fügt ein Seitenauswahl-DropDown hinzu.
    * @param string $name
    * @param null|string $title
    * @return Field\PageSelect
    */
   public function addPageSelect($name, $title)
   {
      $field = new Field\PageSelect($name, $title);
      $this->addFields([$field]);;
      return $field;
   }

   /**
    * Ruft ab ob Felder vorhanden sind.
    * @return bool
    */
   public function hasFields()
   {
      return count($this->fields) > 0;
   }

   public function getField($name)
   {
      foreach ($this->fields as $field) {
         if (implode('/', $field->getName()) == implode('/', is_array($name) ? $name : [$name])) {
            return $field;
         }
      }
   }

}
