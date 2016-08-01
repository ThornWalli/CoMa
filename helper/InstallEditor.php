<?php

namespace CoMa\Helper;

class InstallEditor
{

    private static $hasEditorJS = false;

    public static function editor_html($data)
    {

      $id = $data['id'];
      $name = $data['name'];
        $content = stripslashes($data['content']);

        if (!class_exists('_WP_Editors')) {
            include_once(ABSPATH . 'wp-includes/class-wp-editor.php');
        }

        if (!self::$hasEditorJS) {
            \_WP_Editors::editor_js();
            self::$hasEditorJS = true;
        }
        \_WP_Editors::wp_link_dialog();
        wp_editor($content, $id, ['textarea_name' => $name]);

        $mce_init = self::get_mce_init($id);
        ?>
        <script type="text/javascript">
            (function ($, tinyMCEPreInit) {
                $(function () {
                    tinymce.remove("[name=\"<?php echo $name; ?>\"]");
                    tinyMCEPreInit.mceInit = jQuery.extend(tinyMCEPreInit.mceInit, <?php echo $mce_init ?>);
                    var initData = tinyMCEPreInit.mceInit['<?php echo $id; ?>'];
                    initData.language = 'en';
                    initData.selector = "[name=\"<?php echo $name; ?>\"]";
                    tinyMCE.init(initData);
                });
            })(jQuery, tinyMCEPreInit);

            wpLink.init();

        </script>


        <?php

    }


    private static $mce_settings = null;
    private static $qt_settings = null;

    public static function quicktags_settings($qtInit, $editor_id)
    {
        self::$qt_settings = $qtInit;
        return $qtInit;
    }

    public static function tiny_mce_before_init($mceInit, $editor_id)
    {
//        $mceInit['wpautop'] = true;
        self::$mce_settings = $mceInit;
        return $mceInit;
    }

    /*
    * Code coppied from _WP_Editors class (modified a little)
    */
    private function get_qt_init($editor_id)
    {
        if (!empty(self::$qt_settings)) {
            $options = self::_parse_init(self::$qt_settings);
            $qtInit = "'$editor_id':{$options},";
            $qtInit = '{' . trim($qtInit, ',') . '}';
        } else {
            $qtInit = '{}';
        }
        return $qtInit;
    }

    private function get_mce_init($editor_id)
    {
        if (!empty(self::$mce_settings)) {
            $options = apply_filters('tiny_mce_before_init', self::$mce_settings, $editor_id);
            $options = self::_parse_init($options);
            $mceInit = "'$editor_id':{$options},";
            $mceInit = '{' . trim($mceInit, ',') . '}';
        } else {
            $mceInit = '{}';
        }
        return $mceInit;
    }

    private static function _parse_init($init)
    {
        $options = '';

        foreach ($init as $k => $v) {
            if (is_bool($v)) {
                $val = $v ? 'true' : 'false';
                $options .= $k . ':' . $val . ',';
                continue;
            } elseif (!empty($v) && is_string($v) && (('{' == $v{0} && '}' == $v{strlen($v) - 1}) || ('[' == $v{0} && ']' == $v{strlen($v) - 1}) || preg_match('/^\(?function ?\(/', $v))) {
                $options .= $k . ':' . $v . ',';
                continue;
            }
            $options .= $k . ':"' . $v . '",';
        }

        return '{' . trim($options, ' ,') . '}';
    }

}

?>
