<?php

namespace CoMa\Base;

class Component extends Controller
{
    const TYPE = \CoMa\Helper\Base::TYPE_COMPONENT;

    const TEMPLATE_NAME = 'Component';
    const TEMPLATE_ID = 'default-component';
    const TEMPLATE_PATH = 'component';

    public function __construct($properties = [], $id = null)
    {
        parent::__construct($properties, $id);
        $this->setControls([]);
    }

    public function render($options =['edit' => null])
    {
        $includePath = $this->getTemplatePath() . '.php';

        global $CONTENT_MANAGER_PARENT_COMPONENT;
        $tmpParent = $CONTENT_MANAGER_PARENT_COMPONENT;

        $CONTENT_MANAGER_PARENT_COMPONENT = $this;

        do_action(\CoMa\WP\Action\BEFORE_RENDER, $this);

        if (\CoMa\Helper\Base::isEditMode() && $options['edit'] == null || $options['edit']) {
            include(\CoMa\PLUGIN_TEMPLATE_PATH . 'component.php');
        } else {
            include($includePath);
        }

        do_action(\CoMa\WP\Action\AFTER_RENDER, $this);

        $CONTENT_MANAGER_PARENT_COMPONENT = $tmpParent;
        $includePath = null;
    }

    public function getTemplatePath()
    {

        return \CoMa\THEME_TEMPLATE_PATH . '/component/' . $this::TEMPLATE_PATH;

    }

}

?>