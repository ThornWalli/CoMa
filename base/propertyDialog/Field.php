<?php

namespace CoMa\Base\PropertyDialog {

    class Field
    {

        const PROPERTY_LINK_TYPE = '_link_type';
        const PROPERTY_LINK_INTERNAL_VALUE = '_link_internal_value';
        const PROPERTY_LINK_EXTERNAL_VALUE = '_link_external_value';
        const PROPERTY_LINK_TITLE = '_link_title';
        const PROPERTY_LINK_TARGET = '_link_target';
        const PROPERTY_CODE_MODE = '_code_mode';
        const PROPERTY_CODE_LINE_NUMBERS = '_code_line_numbers';

        private $description;

        private $id;
        private $type = 'text';
        private $cssClass = [];
        private $name;
        private $title;
        private $value;
        private $defaultValue;

        private $checked = false;
        private $selected = false;
        private $disabled = false;
        private $readonly = false;

        private $options = [];
        private $hasLabel = true;
        private $mediaType = null;
        private $items;
        private $rows;
        private $size;

        public function __construct($name = null, $title = null, $value = null, $options = null)
        {
            $this->name = $name;
            $this->title = $title;
            $this->value = $value;
            $this->options = $options;
        }

        /*
         * ##################################################
         * ##################################################
         */

        /**
         * Rendert das Feld mit den angebenen Eigenschaften.
         * @param array $properties
         */
        public function render($properties = [])
        {

            if (empty($this->id) && $this->hasLabel) {
                $this->id(\CoMa\Base\PropertyDialog::uniqid('property-dialog-'));
            }

            $node = $this->getNode($properties);

            include(\CoMa\PLUGIN_TEMPLATE_PATH . 'property-dialog/field.php');

        }

        /**
         * @param array $properties
         * @return string
         */
        public function getNode($properties = [])
        {

            $attributes = [
                'name' => $this->name,
                'class' => implode(' ', is_array($this->getCssClass()) ? $this->getCssClass() : [$this->getCssClass()])
            ];

            $propertyValue = $this->getValue();
            // Take property if available.
            if ($properties && array_key_exists($this->name, $properties)) {
                $propertyValue = $properties[$this->name];
            }

            // When propertyValue empty, set default value
            if ($this->defaultValue && !$propertyValue) {
                $propertyValue = $this->defaultValue;
            }

            if (!is_array($this->options)) {
                $this->options = [];
            }

            return $this->node($this, $properties, $attributes, $propertyValue);

        }

        /**
         * @param Field $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {
            return 'No Field Node';
        }

        /*
         * ##################################################
         * ##################################################
         */

        /**
         * Ruft den Typ ab.
         * @return string
         */
        public function getType()
        {
            return $this->type;
        }

        /**
         * Ruft die CSS-Klassen ab.
         * @return string
         */
        public function getCssClass()
        {
            return $this->cssClass;
        }

        /**
         * Ruft den Wert ab.
         * @return mixed
         */
        public function getValue()
        {
            return $this->value;
        }

        /**
         * Ruft den Default-Wert ab.
         * @return mixed
         */
        public function getDefaultValue()
        {
            return $this->defaultValue;
        }

        /**
         * Ruft den Namen ab.
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
         * Ruft die Beschreibung ab.
         * @return string
         */
        public function getDescription()
        {
            return $this->description;
        }

        /**
         * Ruft die Id ab.
         * @return string
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Ruft den Medien-Typ ab.
         * @return string
         */
        public function getMediaType()
        {
            return $this->mediaType;
        }

        /**
         * @return array
         */
        public function getOptions()
        {
            return $this->options;
        }

        /**
         * @return array
         */
        public function getItems()
        {
            return $this->items;
        }

        /**
         * @return boolean
         */
        public function getHasLabel()
        {
            return $this->hasLabel;
        }

        /**
         * @return integer
         */
        public function getRows()
        {
            return $this->rows;
        }

        /**
         * @return integer
         */
        public function getSize()
        {
            return $this->size;
        }

        /*
         * ##################################################
         */


        /**
         * Ruft ab ob ein Label vorhanden ist.
         * @param boolean $hasLabel
         * @return mixed
         */
        public function hasLabel($hasLabel = null)
        {

            if (empty($hasLabel) && !is_bool($hasLabel)) {
                return $this->hasLabel;
            }

            $this->hasLabel = $hasLabel;
            return $this;
        }

        /**
         * Legt die Id fest.
         * @param string $id
         * @return Field
         */
        public function id($id)
        {
            if ($id != null) {
                $this->id = $id;
            }
            return $this;
        }

        /**
         * Legt den Typ fest.
         * @param string $type
         * @return Field
         */
        public function type($type)
        {
            if ($type != null) {
                $this->type = $type;
            }
            return $this;
        }

        /**
         * Legt die CSS-Klassen fest.
         * @param string $cssClass
         * @return Field
         */
        public function cssClass($cssClass)
        {
            if ($cssClass != null) {
                $this->cssClass = $cssClass;
            }
            return $this;
        }

        /**
         * Legt den Namen fest.
         * @param string $name
         * @return Field
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
         * @return Field
         */
        public function title($title)
        {
            if ($title != null) {
                $this->title = $title;
            }
            return $this;
        }

        /**
         * Legt den Wert fest.
         * @param mixed $value
         * @return Field
         */
        public function value($value)
        {
            if ($value != null) {
                $this->value = $value;
            }
            return $this;
        }

        /**
         * Legt die Zeilenanzahl fest.
         * @param number $rows
         * @return Field
         */
        public function rows($rows)
        {
            if ($rows != null) {
                $this->rows = $rows;
            }
            return $this;
        }

        /**
         * Legt die sichtbaren Zeichenanzahl fest.
         * @param number $size
         * @return Field
         */
        public function size($size)
        {
            if ($size != null) {
                $this->size = $size;
            }
            return $this;
        }

        /**
         * Legt den Media-Typ fest.
         * @param string $mediaType
         * @return Field
         */
        public function mediaType($mediaType)
        {
            if ($mediaType != null) {
                $this->mediaType = $mediaType;
            }
            return $this;
        }

        /*
         * ##################################################
         */

        /**
         * Legt fest ob das Feld ausgewählt ist.
         * @param bool $selected
         * @return Field
         */
        public function selected($selected)
        {
            if ($selected != null) {
                $this->selected = $selected;
            }
            return $this;
        }

        /**
         * Legt fest ob das Feld deaktiviert ist.
         * @param bool $disabled
         * @return Field
         */
        public function disabled($disabled)
        {
            if ($disabled != null) {
                $this->disabled = $disabled;
            }
            return $this;
        }

        /**
         * Legt fest ob das Feld nur Lesbar ist.
         * @param bool $readonly
         * @return Field
         */
        public function readonly($readonly)
        {
            if ($readonly != null) {
                $this->readonly = $readonly;
            }
            return $this;
        }

        /**
         * Legt die Optionen fest.
         * @param array $options
         * @return Field
         */
        public function options($options)
        {
            if ($options != null) {
                $this->options = $options;
            }
            return $this;
        }

        /**
         * Legt den Default-Wert fest.
         * @param mixed $defaultValue
         * @return Field
         */
        public function defaultValue($defaultValue)
        {
            if ($defaultValue != null) {
                $this->defaultValue = $defaultValue;
            }
            return $this;
        }

        /**
         * Legt die Beschreibung fest.
         * @param string $description
         * @return Field
         */
        public function description($description)
        {
            if ($description != null) {
                $this->description = $description;
            }
            return $this;
        }

        /**
         * Legt die Items fest.
         * @param array $items
         * @return Field
         */
        public function items($items)
        {
            if ($items != null) {
                $this->items = $items;
            }
            return $this;
        }

    }

}


namespace CoMa\Base\PropertyDialog\Field {

    use CoMa\Base\PropertyDialog\Field;

    class Button extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name = null, $title = null, $value = null, $options = null)
        {
            parent::__construct($name, $title, $value, $options);
            $this->type('button');
            $this->hasLabel(false);
        }

        /**
         * @param array $properties
         */
        public function render($properties = [])
        {
            if ($this->getValue() == null) {
                $this->value($this->getTitle());
            }
            parent::render();
        }


        /**
         * @param Field\Button $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {
            if ($field->getType() == 'button' || $field->getType() == 'submit') {
                if (!$attributes['class']) {
                    $attributes['class'] = '';
                }
                $attributes['class'] .= ' button button-primary';
            }
            $attributes['type'] = $field->getType();
            $attributes['value'] = $field->getValue();
            return '<input ' . \CoMa\Helper\Base::renderTagAttributes($attributes) . ' />';

        }

    }

    class MultipleValues extends \CoMa\Base\PropertyDialog\Field
    {

        /**
         * @var array<Field>
         */
        private $fields = [];

        public function __construct($name, $title, $fields = [], $value = null, $options = null)
        {
            parent::__construct($name, $title, $value, $options);
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    $this->addFields($field);
                }
            }
        }

        /**
         * @param \CoMa\Base\PropertyDialog\Field $field
         * @return MultipleValues
         */
        public function addFields($field)
        {
            foreach (func_get_args() as $field) {

                $field->name($this->getName() . '[' . $field->getName() . ']');
                $this->fields[] = $field;
            }

            return $this;

        }

        public static function mapValues($values)
        {
            $returnValues = [];
            if ($values) {
                $size = count(current($values));
                for ($i = 0; $i < $size; $i++) {
                    foreach (array_keys($values) as $key) {
                        $returnValues[$i][$key] = $values[$key][$i];
                    }
                }
            }
            return $returnValues;
        }

        public function getFields()
        {
            return $this->fields;
        }

        /**
         * @param Field\MultipleValues $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {

            ob_start();

            ?>

            <div class="coma-controller partial" data-coma-controller="components/MultipleValues"
                 data-partial="coma/component/field/multiple-values">

                <script type="text/template">
                    <div class="fields">
                        <a class="remove icon dashicons dashicons-no" href="#"></a>
                        <div>
                            <?php

                            foreach ($field->getFields() as $multiField) {
                                /**
                                 * @var \CoMa\Base\PropertyDialog\Field $multiField
                                 */
                                $multiField->hasLabel(false);
                                $multiField->id('property-dialog-id');
                                echo '<div style="width: ' . (100 / count($field->getFields())) . '%;">';
                                $multiField->render();
                                echo '</div>';
                            }

                            ?>
                        </div>
                    </div>
                </script>

                <div class="rows">
                    <?php
                    //
                    $sources = self::mapValues($properties[$field->getName()]);
                    foreach ($sources as $key => $source) {

                        ?>

                        <div class="fields">
                            <div class="remove">
                                <input type="button" class="button button-small" value="X"/>
                            </div>

                            <div>

                                <?php

                                foreach ($field->getFields() as $multiField) {

                                    /**
                                     * @var \CoMa\Base\PropertyDialog\Field $multiField
                                     */
                                    $multiField->id(\CoMa\Base\PropertyDialog::uniqid('property-dialog-'));
                                    $multiField->value($source[preg_replace('/.*\[(.*)\]/', '$1', $multiField->getName())]);
                                    echo '<div style="width: ' . (100 / count($field->getFields())) . '%;">';
                                    $multiField->render();
                                    echo '</div>';
                                }
                                ?>

                            </div>

                        </div>

                        <?php
                    }

                    ?>
                </div>


                <div>
                    <input type="button" class="button button-secondary add-field"
                           value="<?php echo __('Add Field', \CoMa\PLUGIN_NAME); ?>"/>
                </div>

            </div>

            <?php

            $node = ob_get_clean();
            return $node;

        }


    }

    class DateSelect extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title = null, $value = null)
        {
            parent::__construct($name, $title, $value);
        }

        /**
         * @param Field\DateSelect $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {

            ob_start();

            $firstPage = get_posts([
                'post_status' => 'publish',
                'order' => 'ASC',
                'orderby' => 'post_date',
                'post_type' => 'post',
                'posts_per_page' => null
            ])[0];
            $lastPage = get_posts([
                'post_status' => 'publish',
                'order' => 'DESC',
                'orderby' => 'post_date',
                'post_type' => 'post',
                'posts_per_page' => null
            ])[0];

            if ($propertyValue) {
                $time = strtotime($propertyValue);
            } else {
                $time = time();
            }

            $day = date('d', $time);
            $month = date('m', $time);
            $year = date('Y', $time);

            ?>

            <div class="coma-controller partial" data-coma-controller="components/field/DateSelect"
                 data-partial="coma/component/field/date-select"
                 data-date="<?php echo $propertyValue; ?>">

                <input
                    type="hidden" <?php echo \CoMa\Helper\Base::renderTagAttributes($attributes); ?>>

                <select data-type="day">
                    <option value="0"></option>
                    <?php

                    for ($i = 1; $i <= 31; $i++) {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }

                    ?>
                </select>

                <select data-type="month">
                    <option value="0"></option>
                    <?php

                    for ($i = 1; $i <= 12; $i++) {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }

                    ?>
                </select>

                <select data-type="year">
                    <option value="0"></option>
                    <?php

                    for ($i = date('Y', strtotime($firstPage->post_date)); $i <= date('Y', strtotime($lastPage->post_date)); $i++) {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }

                    ?>
                </select>

                <a class="reset icon dashicons dashicons-no"></a>

            </div>


            <?php

            $node = ob_get_clean();
            return $node;

        }


    }

    class Link extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title)
        {
            parent::__construct($name, $title);
        }

        /**
         * @param Field\Link $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {

            ob_start();

            $pageId = null;
            if ($properties[$field->getName() . Field::PROPERTY_LINK_INTERNAL_VALUE]) {
                $pageId = $properties[$field->getName() . Field::PROPERTY_LINK_INTERNAL_VALUE];
            }

            ?>
            <div class="coma-controller partial" data-coma-controller="components/field/LinkSelect"
                 data-partial="coma/component/field/link-select"
                 data-type="<?php echo $properties[$field->getName() . Field::PROPERTY_LINK_TYPE]; ?>">

                <input type="hidden" class="page-type-value"
                       name="<?php echo $field->getName() . Field::PROPERTY_LINK_INTERNAL_VALUE; ?>"
                       value="<?php echo $pageId; ?>"/>

                <div class="input external">
                    <input type="text" name="<?php echo $field->getName() . Field::PROPERTY_LINK_EXTERNAL_VALUE; ?>"
                           placeholder="url..."
                           value="<?php echo $properties[$field->getName() . Field::PROPERTY_LINK_EXTERNAL_VALUE]; ?>">
                </div>
                <div class="input internal">

                    <div class="page">

                        <?php

                        wp_dropdown_pages(['class' => 'page-value', 'selected' => $pageId, 'posts_per_page' => -1]);

                        ?>

                    </div>
                    <div class="post">

                        <select class="post-value">

                            <?php

                            $posts = get_posts(['posts_per_page' => -1]);
                            foreach ($posts as $post) {

                                ?>

                                <option
                                    value="<?php echo $post->ID; ?>"<?php if ($pageId == $post->ID) {
                                    echo ' selected="selected"';
                                } ?>><?php echo $post->post_title; ?></option>

                                <?php

                            }

                            ?>

                        </select>

                    </div>
                </div>

                <select class="link-type" name="<?php echo $field->getName() . Field::PROPERTY_LINK_TYPE; ?>">

                    <option
                        value="internal" <?php if ($properties[$field->getName() . Field::PROPERTY_LINK_TYPE] == 'internal') {
                        echo ' selected';
                    } ?>>Internal
                    </option>
                    <option
                        value="external" <?php if ($properties[$field->getName() . Field::PROPERTY_LINK_TYPE] == 'external') {
                        echo ' selected';
                    } ?>>External
                    </option>

                </select>

                <select class="link-page-type">

                    <option
                        value="page" <?php if (get_post_type($pageId) == 'page') {
                        echo ' selected';
                    } ?>><?php echo __('Page', \CoMa\PLUGIN_NAME); ?>
                    </option>
                    <option
                        value="post" <?php if (get_post_type($pageId) == 'post') {
                        echo ' selected';
                    } ?>><?php echo __('Post', \CoMa\PLUGIN_NAME); ?>
                    </option>

                </select>

                <br/>

                <label><?php echo __('Link-Title', \CoMa\PLUGIN_NAME); ?></label>

                <div class="input">
                    <input type="text"
                           name="<?php echo $field->getName() . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_TITLE; ?>"
                           placeholder="Titel..."
                           value="<?php if ($properties[$field->getName() . Field::PROPERTY_LINK_TITLE]) {
                               echo $properties[$field->getName() . Field::PROPERTY_LINK_TITLE];
                           } ?>">
                </div>
                <select class="link-target"
                        name="<?php echo $field->getName() . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_TARGET; ?>">
                    <option value="" <?php if ($properties[$field->getName() . Field::PROPERTY_LINK_TARGET] == '') {
                        echo ' selected';
                    } ?>>
                    </option>
                    <option
                        value="_blank" <?php if ($properties[$field->getName() . Field::PROPERTY_LINK_TARGET] == '_blank') {
                        echo ' selected';
                    } ?>><?php echo __('New Window/Tab', \CoMa\PLUGIN_NAME); ?> ( _blank )
                    </option>
                    <option
                        value="_parent" <?php if ($properties[$field->getName() . Field::PROPERTY_LINK_TARGET] == '_parent') {
                        echo ' selected';
                    } ?>><?php echo __('Parent Window/Tab', \CoMa\PLUGIN_NAME); ?> ( _parent )
                    </option>
                    <option
                        value="_self" <?php if ($properties[$field->getName() . Field::PROPERTY_LINK_TARGET] == '_self') {
                        echo ' selected';
                    } ?>><?php echo __('Current Window/Tab', \CoMa\PLUGIN_NAME); ?> ( _self )
                    </option>
                    <option
                        value="_top" <?php if ($properties[$field->getName() . Field::PROPERTY_LINK_TARGET] == '_top') {
                        echo ' selected';
                    } ?>><?php echo __('Top Window/Tab', \CoMa\PLUGIN_NAME); ?> ( _top )
                    </option>
                </select>

            </div>
            <?php

            $node = ob_get_clean();
            return $node;

        }


    }

    class ColorPicker extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title)
        {
            parent::__construct($name, $title);
        }

        /**
         * @param Field\ColorPicker $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {

            ob_start();

            $attributes['default-color'] = $field->getDefaultValue();
            $attributes['value'] = $propertyValue;

            ?>
            <div class="coma-controller partial" data-coma-controller="components/field/ColorPicker"
                 data-partial="coma/component/field/color-picker">
                <input <?php echo \CoMa\Helper\Base::renderTagAttributes($attributes); ?> type="text"
                                                                                          maxlength="7"
                                                                                          placeholder="<?php esc_attr_e($field->getDefaultValue()); ?>" <?php echo $propertyValue; ?> />
            </div>

            <?php

            return ob_get_clean();

        }


    }

    class MenuPositionSelect extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title = null, $value = null)
        {
            parent::__construct($name, $title, $value);
        }

        /**
         * @param Field\MenuPositionSelect $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {

            if ($field->getValue())
                $attributes['value'] = $field->getValue();
            if ($field->getId() || $field->getHasLabel())
                $attributes['id'] = $field->getId();
            if ($field->getRows())
                $attributes['size'] = $field->getSize();
            else
                $attributes['size'] = $field->getSize();

            ob_start();

            ?>

            <select<?php echo \CoMa\Helper\Base::renderTagAttributes($attributes); ?>>

                <option value=""></option>

                <?php

                foreach (get_registered_nav_menus() as $location => $description) {

                    ?>

                    <option
                        value="<?php echo $location; ?>"<?php if ($propertyValue == $location) {
                        echo ' selected="selected"';
                    } ?>><?php echo $description; ?></option>

                    <?php

                }


                ?>

            </select>

            <?php

            return ob_get_clean();

        }


    }

    class CategorySelect extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title = null, $value = null)
        {
            parent::__construct($name, $title, $value);
        }

        /**
         * @param Field\CategorySelect $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {

            $attributes['multiple'] = true;
            if ($field->getValue())
                $attributes['value'] = $field->getValue();
            if ($field->getId() || $field->getHasLabel())
                $attributes['id'] = $field->getId();
            if ($field->getRows())
                $attributes['size'] = $field->getSize();
            else
                $attributes['size'] = $field->getSize();

            ob_start();

            ?>

            <select<?php echo \CoMa\Helper\Base::renderTagAttributes($attributes); ?>>

                <?php

                function hasChildrens($parent)
                {
                    return count(get_categories(['parent' => $parent, 'hide_empty' => false]));
                }

                function getOptions($selected = [], $parent = 0)
                {
                    if (!is_array($selected)) {
                        $selected = [$selected];
                    }

                    $categories = get_categories(['parent' => $parent, 'hide_empty' => false]);
                    foreach ($categories as $category) {

                        ?>

                        <option
                            value="<?php echo $category->cat_ID; ?>"<?php if (in_array($category->cat_ID, $selected)) {
                            echo ' selected="selected"';
                        } ?>><?php echo $category->name; ?></option>

                        <?php

                        if (hasChildrens($category->cat_ID) > 0) {

                            ?>

                            <optgroup label="<?php echo $category->name; ?>">
                                <?php getOptions($selected, $category->cat_ID); ?>
                            </optgroup>

                            <?php

                        }
                    }

                }

                getOptions($properties['category'], 0);


                ?>

            </select>

            <?php

            return ob_get_clean();

        }


    }

    class PageSelect extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title)
        {
            parent::__construct($name, $title);
        }

        /**
         * @param Field\PageSelect $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {
            return wp_dropdown_pages(['posts_per_page' => -1, 'echo' => false, 'name' => $field->getName(), 'selected' => $propertyValue]);
        }

    }

    class PostSelect extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title = null, $value = null)
        {
            parent::__construct($name, $title, $value);
        }

        /**
         * @param Field\PostSelect $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {

            if ($field->getOptions()['multiple']) {
                $attributes['multiple'] = true;
            }
            if ($field->getValue())
                $attributes['value'] = $field->getValue();
            if ($field->getId() || $field->getHasLabel())
                $attributes['id'] = $field->getId();
            if ($field->getRows())
                $attributes['size'] = $field->getSize();
            else
                $attributes['size'] = $field->getSize();

            ob_start();

            ?>

            <select<?php echo \CoMa\Helper\Base::renderTagAttributes($attributes); ?>>


                <?php

                if (!$attributes['multiple']) {
                    echo '<option value="">' . __('No post', \CoMa\PLUGIN_NAME) . '</option>';
                }

                if ($properties['post']) {
                    $selected = [$properties['post']];
                } else if ($properties['posts']) {
                    if (!is_array($properties['posts'])) {
                        $selected = [$properties['posts']];
                    } else
                        $selected = $properties['posts'];
                } else {
                    $selected = [];
                }

                $posts = get_posts(['posts_per_page' => -1]);
                foreach ($posts as $post) {

                    ?>

                    <option
                        value="<?php echo $post->ID; ?>"<?php if (in_array($post->ID, $selected)) {
                        echo ' selected="selected"';
                    } ?>><?php echo $post->post_title; ?></option>

                    <?php

                }


                ?>

            </select>

            <?php

            return ob_get_clean();

        }


    }

    class CodeEditor extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title = null, $value = null, $options = null)
        {
            parent::__construct($name, $title, $value, $options);
            $this->hasLabel(false);
        }

        /**
         * @param Field\CodeEditor $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {
            if ($field->getRows()){
                $attributes['rows'] = $field->getRows();
            }

            ob_start();

            ?>

            <div class="coma-controller partial" data-coma-controller="components/field/CodeEditor"
                 data-partial="coma/component/field/code-editor">
                <label><?php echo $field->getTitle() . '-' . __('Editor-Mode', \CoMa\PLUGIN_NAME); ?></label>
                <select class="mode" name="<?php echo $field->getName() . Field::PROPERTY_CODE_MODE; ?>">
                    <?php

                    foreach ([
                                 'html' => 'HTML',
                                 'css' => 'CSS',
                                 'javascript' => 'Javascript',
                                 'php' => 'PHP',
                                 'sql' => 'SQL'
                             ] as $value => $title) {
                        ?>
                        <option value="<?php echo $value; ?>"<?php

                        if (array_key_exists($field->getName() . Field::PROPERTY_CODE_MODE, $properties) &&
                            $properties[$field->getName() . Field::PROPERTY_CODE_MODE] == $value
                        ) {
                            echo ' selected="selected"';
                        }

                        ?>><?php echo $title; ?></option><?php
                    }

                    ?>
                </select>
                <textarea class="code"
                          name="<?php echo $field->getName(); ?>" <?php echo \CoMa\Helper\Base::renderTagAttributes($attributes); ?>><?php echo $propertyValue; ?></textarea>
            </div>
            <?php

            return ob_get_clean();
        }


    }

    class Editor extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title = null, $value = null, $options = null)
        {
            parent::__construct($name, $title, $value, $options);
        }

        /**
         * @param Field\Editor $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {
            global $CONTENT_MANAGER_EDITOR_DISABLE;
            if ($CONTENT_MANAGER_EDITOR_DISABLE) {
                return '<textarea' . \CoMa\Helper\Base::renderTagAttributes($attributes) . '>' . $propertyValue . '</textarea>';
            } else {
                ob_start();
                do_action(\CoMa\WP\Action\EDITOR_HTML, ['id' => $attributes['name'], 'content' => $propertyValue]);
                return ob_get_clean();
            }
        }


    }

    class MediaSelect extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title = null, $mediaId = null, $options = null)
        {
            parent::__construct($name, $title, $mediaId, $options);
        }

        /**
         * @param Field $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {
            $id = $propertyValue;
            ob_start();

            ?>
            <div class="coma-controller partial"
                 data-coma-controller="components/field/MediaSelect"
                 data-partial="coma/component/field/media-select"<?php echo $id ? ' data-id="' . $id . '"' : ''; ?>>
                <div class="input">
                    <input type="hidden" name="<?php echo $field->getName(); ?>" value="<?php echo $id; ?>"/>
                    <input type="text" value="" readonly="readonly"/>
                    <div class="buttons">
                        <input type="button" class="button button-primary select"
                               value="<?php echo __('Select', \CoMa\PLUGIN_NAME); ?>">
                        <input type="button" class="button button-primary remove"
                               value="<?php echo __('Remove', \CoMa\PLUGIN_NAME); ?>">
                    </div>
                </div>
                <div class="preview">
                    <img src=""/>
                </div>
            </div>
            <?php

            return ob_get_clean();
        }


    }


    class TextField extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title = null, $value = null, $options = null)
        {
            parent::__construct($name, $title, $value, $options);
            $this->type('text');
        }

        /**
         * @param Field\TextField $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {
            if ($field->getType())
                $attributes['type'] = $field->getType();
            if ($field->getTitle())
                $attributes['placeholder'] = $field->getTitle();
            if ($propertyValue)
                $attributes['value'] = $propertyValue;
            if ($propertyValue)
                $attributes['value'] = $propertyValue;;
            if (empty($attributes['value'])) {
                $attributes['value'] = $field->getDefaultValue();
            }
            if ($field->getId() || $field->getHasLabel())
                $attributes['id'] = $field->getId();

            return '<input' . \CoMa\Helper\Base::renderTagAttributes($attributes) . ' />';
        }


    }

    class TextArea extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title = null, $value = null, $options = null)
        {
            parent::__construct($name, $title, $value, $options);
        }

        /**
         * @param Field\TextArea $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {
            return '<textarea' . \CoMa\Helper\Base::renderTagAttributes($attributes) . '>' . $propertyValue . '</textarea>';
        }

    }

    class DropDown extends \CoMa\Base\PropertyDialog\Field
    {

        public function __construct($name, $title = null, $items = null, $value = null, $options = null)
        {
            parent::__construct($name, $title, $value, $options);
            $this->items($items);
        }

        /**
         * @param Field\DropDown $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {

            $node = '<select' . \CoMa\Helper\Base::renderTagAttributes(array_merge($attributes, $field->getOptions())) . '>';
            if ($field->getItems()) {
                foreach ($field->getItems() as $item => $key) {

                    $node .= '<option';
                    if ($propertyValue == $key || is_array($propertyValue) && in_array($key, $propertyValue)) {
                        $node .= ' selected="selected"';
                    }
                    $node .= ' value="' . $key . '">' . $item . '</option>';

                }
            }
            return $node . '</select>';
        }


    }

    class Checkbox extends \CoMa\Base\PropertyDialog\Field
    {

        /**
         * @var bool
         */
        private $isRadio = false;
        private $checked = false;
        private $defaultChecked = false;


        public function __construct($name, $title = null, $value = null, $options = null)
        {
            parent::__construct($name, $title, $value, $options);
        }

        /**
         * @param Field\Checkbox $field
         * @param array $properties
         * @param array $attributes
         * @param mixed $propertyValue
         * @return string
         */
        public static function node($field, $properties, $attributes, $propertyValue)
        {

            $attributes['type'] = ($field->getIsRadio() ? 'radio' : 'checkbox');
            if ($field->getValue())
                $attributes['value'] = $field->getValue();
            if ($propertyValue && !$field->getIsRadio())
                $attributes['value'] = $propertyValue;
            if (empty($attributes['value'])) {
                $attributes['value'] = $field->getDefaultValue();
            }
            if ($field->getId() || $field->getHasLabel())
                $attributes['id'] = $field->getId();

            if (is_bool($field->getDefaultChecked()) && $field->getDefaultChecked()) {
                $attributes['checked'] = $field->getDefaultChecked();
            }

            $attributes['checked'] = ($field->getChecked() || !empty($properties[$field->getName()]) && $properties[$field->getName()] == $propertyValue) ? true : false;

            return '<input' . \CoMa\Helper\Base::renderTagAttributes($attributes) . ' />';
        }

        /**
         * @return bool
         */
        private function getIsRadio()
        {
            return $this->isRadio;
        }

        /**
         * @param bool $isRadio
         * @return \CoMa\Base\PropertyDialog\Field\Checkbox
         */
        public function isRadio($isRadio)
        {
            $this->isRadio = $isRadio;
            return $this;
        }

        /**
         * Legt fest ob die Checkbox default ausgewählt ist.
         * @param bool $defaultChecked
         * @return Field\Checkbox
         */
        public function defaultChecked($defaultChecked)
        {
            if ($defaultChecked != null) {
                $this->defaultChecked = $defaultChecked;
            }
            return $this;
        }

        /**
         * @return bool
         */
        private function getDefaultChecked()
        {
            return $this->defaultChecked;
        }


        /**
         * Legt fest ob die Checkbox ausgewählt ist.
         * @param bool $checked
         * @return Field\Checkbox
         */
        public function checked($checked)
        {
            if ($checked != null) {
                $this->checked = $checked;
            }
            return $this;
        }


        /**
         * @return boolean
         */
        public function getChecked()
        {
            return $this->checked;
        }


    }

}


?>