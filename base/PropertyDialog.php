<?php

namespace CoMa\Base {

    class PropertyDialog
    {

        /**
         * @var array
         */
        private $tabs = [];
        /**
         * @var string
         */
        private $title = [];

        /**
         * @var array<\CoMa\Base\PropertyDialog\Button>
         */
        private $buttons = [];


        public function __construct()
        {
            $this->addTab('default', 'Default');
            $this->addButton('apply', 'Übernehmen');
        }


        private function addButton($name, $title)
        {
            $button = new \CoMa\Base\PropertyDialog\Field\Button();
            $button->name($name)->title($title);
            $this->buttons[] = $button;
            return $button;
        }

        /**
         * Erzeugt ein neues Tab.
         * @param $name
         * @param $title
         * @param array $fields
         * @return PropertyDialog\Tab
         */
        public function addTab($name, $title, $fields = [])
        {
            $tab = null;
            if (!array_key_exists($name, $this->tabs)) {
                $tab = new \CoMa\Base\PropertyDialog\Tab($name, $title, $fields);
                $this->tabs[$name] = $tab;
            } else {
                /**
                 * @type PropertyDialog\Tab $tab
                 */
                $tab = $this->tabs[$name];
                if (count($fields) > 0) {
                    $tab->addFields($fields);
                }
            }
            return $tab;
        }

        /**
         * Für die angegebenen Felder hinzu.
         * @param $fields
         * @param string $tabName
         * @return $this
         */
        public function addFields($fields, $tabName = 'default')
        {
            array_push($this->tabs[$tabName], $fields);
            return $this;
        }

        /**
         * Rendert den Dialog mit den angebenen Eigenschaften.
         * @param array $properties
         */
        public function render($properties = [])
        {
            include(\CoMa\PLUGIN_TEMPLATE_PATH . 'property-dialog.php');
        }

        /**
         * @param string $name
         * @return PropertyDialog\Tab
         */
        public function getTab($name = 'default')
        {
            return $this->tabs[$name];
        }

        /**
         * Ruft ein uniq Id ab, mit oder ohne Prefix.
         * @param string $prefix
         * @return string
         */
        public static function uniqid($prefix = '')
        {
            return $prefix . md5(uniqid(rand()));
        }


        /*
     * ##################################################
     * ##################################################
     */

        /**
         * Ruft den Titel ab.
         * @return string
         */
        public function getTitle()
        {
            return $this->title;
        }

        /*
         * ##################################################
         */

        /**
         * Legt den Namen fest.
         * @param string $name
         * @return PropertyDialog
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
         * @return PropertyDialog
         */
        public function title($title)
        {
            if ($title != null) {
                $this->title = $title;
            }
            return $this;
        }

        /**
         * Ruft alle Feldnamen ab.
         * @return array
         */
        public function getAllFieldNames()
        {
            $names = [];
            foreach ($this->tabs as $tab) {
                /**
                 * @type PropertyDialog\Tab $tab
                 */
                $names = array_merge($names, $tab->getFieldNames());
            }
            return $names;
        }

        /**
         * Ruft alle Feldtypen ab.
         * @return array
         */
        public function getAllFieldTypes()
        {
            $types = [];
            foreach ($this->tabs as $tab) {
                /**
                 * @type PropertyDialog\Tab $tab
                 */
                $types = array_merge($types, $tab->getFieldTypes());
            }
            return $types;
        }


    }

}