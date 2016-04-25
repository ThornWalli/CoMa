<?php

namespace CoMa\Base;

use CoMa\Helper\Base;

class Area extends Controller
{
    const TYPE =Base::TYPE_AREA;

    const TEMPLATE_NAME = 'Area Component';
    const TEMPLATE_ID = 'default-area';
    const TEMPLATE_PATH = 'area';

    const COMPONENT_ALIGNMENT = 'vertical';

    public function __construct($properties = [], $id = null)
    {
        parent::__construct($properties, $id);
        $this->setControls([]);
    }

    public function render()
    {

        $this->setControls([]);

        if ($this->getDisabled() && !Base::isEditMode()) {
            return;
        }

        $includePath = $this->getTemplatePath() . '.php';
        global $CONTENT_MANAGER_PARENT_COMPONENT;
        $tmpParent = $CONTENT_MANAGER_PARENT_COMPONENT;

        do_action(\CoMa\WP\Action\BEFORE_RENDER, $this);

        $CONTENT_MANAGER_PARENT_COMPONENT = $this;
        if (Base::isEditMode()) {
            include(\CoMa\PLUGIN_TEMPLATE_PATH . 'area.php');
        } else {
            include($includePath);
        }

        do_action(\CoMa\WP\Action\AFTER_RENDER, $this);

        $CONTENT_MANAGER_PARENT_COMPONENT = $tmpParent;
        $includePath = null;

    }

    public function getTemplatePath()
    {
        return \CoMa\THEME_TEMPLATE_PATH . '/area' . self::TEMPLATE_PATH;
    }

    /**
     * Ruft alle Componten ab, die in der Area verwendet werden können
     * @return array
     */
    public static function getClasses()
    {
        return [];
    }


}

?>