<?php

namespace CoMa\Base;

use CoMa\Helper\Base;

class GlobalProperties extends PropertyHandler
{

    public function load()
    {
        if (Base::getWPOption(\CoMa\WP\Options\GLOBAL_PROPERTIES)) {
            return Base::getWPOption(\CoMa\WP\Options\GLOBAL_PROPERTIES);
        } else {
            return null;
        }
    }

    public function save()
    {
        Base::setWPOption(\CoMa\WP\Options\GLOBAL_PROPERTIES, $this->get());
    }

}

?>