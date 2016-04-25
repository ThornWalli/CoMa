<?php

namespace CoMa\Helper;

class Property
{

    /**
     * Gibt alle Eigenschaftsnamen mit dem angegebenen Namen im Code-Editor zurück.
     * @param $name
     * @return array
     */
    public static function getCodeEditorProperties($name)
    {
        return [$name . \CoMa\Base\PropertyDialog\Field::PROPERTY_CODE_MODE];
    }

    /**
     * Gibt alle Eigenschaftsnamen mit dem angegebenen Namen vom Link zurück.
     * @param $name
     * @return array
     */
    public static function getLinkProperties($name)
    {
        return [$name . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_TYPE, $name . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_INTERNAL_VALUE, $name . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_EXTERNAL_VALUE, $name . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_TITLE, $name . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_TARGET];
    }

    public static function getLinkUrl($name, $properties)
    {
        if ($properties[$name . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_TYPE] == 'internal') {
            return get_permalink($properties[$name . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_INTERNAL_VALUE]);
        } else {
            return $properties[$name . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_EXTERNAL_VALUE];
        }
    }

    public static function getLinkTitle($name, $properties)
    {
        if (!$properties[$name . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_TITLE] && $properties[$name . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_TYPE] == 'internal') {
            return get_the_title($properties[$name . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_INTERNAL_VALUE]);
        } else {
            return $properties[$name . \CoMa\Base\PropertyDialog\Field::PROPERTY_LINK_TITLE];
        }
    }

}