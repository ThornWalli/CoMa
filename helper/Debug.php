<?php

namespace CoMa\Helper;

class Debug
{

    public static function init()
    {
        if (\CoMa\Helper\Base::getWPOption(\CoMa\WP\Options\DEBUG_SHOW_ELEMENTS)) {
            wp_enqueue_style('coma.css', \CoMa\PLUGIN_URL . 'css/debug.css', [], \CoMa\VERSION);
        }

        if (\CoMa\Helper\Base::getWPOption(\CoMa\WP\Options\DEBUG_LIVERELOAD)) {
            self::livereload();
        }
    }

    private static function livereload()
    {
        function livereloadTag()
        {
            /**
             * Livereload requires running grunt default task.
             */
            ?>
            <script>document.write('<script src="http://'
                    + (location.host || 'localhost').split(':')[0]
                    + ':35730/livereload.js?snipver=1"></'
                    + 'script>')</script>
            <?php
        }

        add_action('wp_footer', function () {
            echo livereloadTag();
        });
        add_action('admin_footer', function () {
            echo livereloadTag();
        });
    }


}

?>