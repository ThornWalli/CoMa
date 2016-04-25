<?php

namespace CoMa\Helper;

define('CoMa\Helper\InstallOptions\GROUP_GENERAL', \CoMa\PREFIX . '_general');
define('CoMa\Helper\InstallOptions\SECTION_GENERAL', \CoMa\PREFIX . '_general');
define('CoMa\Helper\InstallOptions\SECTION_DEBUG', \CoMa\PREFIX . '_debug');
define('CoMa\Helper\InstallOptions\SECTION_CACHE_PAGE_POST', \CoMa\PREFIX . '_cache_page_post');
define('CoMa\Helper\InstallOptions\SECTION_CACHE_MEDIA', \CoMa\PREFIX . '_cache_media');

class InstallOptions
{

    public static function init()
    {

        register_setting(\CoMa\Helper\InstallOptions\GROUP_GENERAL, Base::getPrefixedName(\CoMa\WP\Options\DEBUG_LIVERELOAD));
        register_setting(\CoMa\Helper\InstallOptions\GROUP_GENERAL, Base::getPrefixedName(\CoMa\WP\Options\DEBUG_SHOW_ELEMENTS));
        register_setting(\CoMa\Helper\InstallOptions\GROUP_GENERAL, Base::getPrefixedName(\CoMa\WP\Options\USE_WP_PAGE_POST_REVISION));
        register_setting(\CoMa\Helper\InstallOptions\GROUP_GENERAL, Base::getPrefixedName(\CoMa\WP\Options\CACHE_PAGE));
        register_setting(\CoMa\Helper\InstallOptions\GROUP_GENERAL, Base::getPrefixedName(\CoMa\WP\Options\CACHE_POST));
        register_setting(\CoMa\Helper\InstallOptions\GROUP_GENERAL, Base::getPrefixedName(\CoMa\WP\Options\CACHE_PATH));
        register_setting(\CoMa\Helper\InstallOptions\GROUP_GENERAL, Base::getPrefixedName(\CoMa\WP\Options\CACHE_MEDIA_PNG_QUALITY));
        register_setting(\CoMa\Helper\InstallOptions\GROUP_GENERAL, Base::getPrefixedName(\CoMa\WP\Options\CACHE_MEDIA_JPEG_QUALITY));

        add_settings_section(
            \CoMa\Helper\InstallOptions\SECTION_GENERAL,
            __('General'),
            function () {
            },
            \CoMa\Helper\InstallOptions\GROUP_GENERAL
        );

        add_settings_field(
            Base::getPrefixedName(\CoMa\WP\Options\USE_WP_PAGE_POST_REVISION),
            __('Use Post-Revision from Wordpress?', \CoMa\PLUGIN_NAME),
            '\CoMa\Helper\InstallOptions::renderField',
            \CoMa\Helper\InstallOptions\GROUP_GENERAL,
            \CoMa\Helper\InstallOptions\SECTION_GENERAL, [
                'type' => 'checkbox',
                'name' => Base::getPrefixedName(\CoMa\WP\Options\USE_WP_PAGE_POST_REVISION),
                'desc' => __('<code>Warning: Experimentally, not tested</code> <br />Enables Pages/Post Revisions-Tool from WordPress, for areas and components.', \CoMa\PLUGIN_NAME)
            ]
        );

        add_settings_section(
            \CoMa\Helper\InstallOptions\SECTION_CACHE_PAGE_POST,
            __('Pages/Posts Cache', \CoMa\PLUGIN_NAME),
            function () {
                echo __('Save pages/posts html-markup by first load as flat file for other requests.', \CoMa\PLUGIN_NAME);
            },
            \CoMa\Helper\InstallOptions\GROUP_GENERAL
        );


        add_settings_field(
            Base::getPrefixedName(\CoMa\WP\Options\CACHE_PAGE),
            __('Pages-Cache activate?', \CoMa\PLUGIN_NAME),
            '\CoMa\Helper\InstallOptions::renderField',
            \CoMa\Helper\InstallOptions\GROUP_GENERAL,
            \CoMa\Helper\InstallOptions\SECTION_CACHE_PAGE_POST, [
                'type' => 'checkbox',
                'name' => Base::getPrefixedName(\CoMa\WP\Options\CACHE_PAGE)
            ]
        );
        add_settings_field(
            Base::getPrefixedName(\CoMa\WP\Options\CACHE_POST),
            __('Posts-Cache activate?', \CoMa\PLUGIN_NAME),
            '\CoMa\Helper\InstallOptions::renderField',
            \CoMa\Helper\InstallOptions\GROUP_GENERAL,
            \CoMa\Helper\InstallOptions\SECTION_CACHE_PAGE_POST, [
                'type' => 'checkbox',
                'name' => Base::getPrefixedName(\CoMa\WP\Options\CACHE_POST)
            ]
        );
        add_settings_field(
            Base::getPrefixedName(\CoMa\WP\Options\CACHE_PATH),
            __('Cache-Path:', \CoMa\PLUGIN_NAME),
            '\CoMa\Helper\InstallOptions::renderField',
            \CoMa\Helper\InstallOptions\GROUP_GENERAL,
            \CoMa\Helper\InstallOptions\SECTION_CACHE_PAGE_POST, [
                'type' => 'text',
                'name' => Base::getPrefixedName(\CoMa\WP\Options\CACHE_PATH),
                'desc' => __('Path to save cached pages as file.', \CoMa\PLUGIN_NAME) . '<br /><code>' .
                    __('DEFAULT: Wordpress-Root', \CoMa\PLUGIN_NAME) . '<br />' .
                    __('%PLUGIN_PATH%: Plugin-Path', \CoMa\PLUGIN_NAME) . '<br />' .
                    __('%THEME_PATH%: Theme-Path', \CoMa\PLUGIN_NAME) . '</code>',
                'placeholder' => 'Default: %PLUGIN_PATH%/cache'
            ]
        );

        add_settings_section(
            \CoMa\Helper\InstallOptions\SECTION_CACHE_MEDIA,
            __('Media Cache', \CoMa\PLUGIN_NAME), null,
            \CoMa\Helper\InstallOptions\GROUP_GENERAL
        );

        $options = [];

        for ($i = 100; $i > 0; $i -= 10) {
            $options[$i] = $i;
        }

        add_settings_field(
            Base::getPrefixedName(\CoMa\WP\Options\CACHE_MEDIA_PNG_QUALITY),
            'PNG ' . __('Image Quality?', \CoMa\PLUGIN_NAME),
            '\CoMa\Helper\InstallOptions::renderField',
            \CoMa\Helper\InstallOptions\GROUP_GENERAL,
            \CoMa\Helper\InstallOptions\SECTION_CACHE_MEDIA, [
                'type' => 'select',
                'name' => Base::getPrefixedName(\CoMa\WP\Options\CACHE_MEDIA_PNG_QUALITY),
                'options' => $options,
                'select' => \CoMa\Helper\Base::getWPOption(\CoMa\WP\Options\CACHE_MEDIA_PNG_QUALITY)
            ]
        );
        add_settings_field(
            Base::getPrefixedName(\CoMa\WP\Options\CACHE_MEDIA_JPEG_QUALITY),
            'JPEG ' . __('Image Quality?', \CoMa\PLUGIN_NAME),
            '\CoMa\Helper\InstallOptions::renderField',
            \CoMa\Helper\InstallOptions\GROUP_GENERAL,
            \CoMa\Helper\InstallOptions\SECTION_CACHE_MEDIA, [
                'type' => 'select',
                'name' => Base::getPrefixedName(\CoMa\WP\Options\CACHE_MEDIA_JPEG_QUALITY),
                'options' => $options,
                'select' => \CoMa\Helper\Base::getWPOption(\CoMa\WP\Options\CACHE_MEDIA_JPEG_QUALITY)
            ]
        );


        add_settings_section(
            \CoMa\Helper\InstallOptions\SECTION_DEBUG,
            __('Debug'),
            function () {
            },
            \CoMa\Helper\InstallOptions\GROUP_GENERAL
        );

        add_settings_field(
            Base::getPrefixedName(\CoMa\WP\Options\DEBUG_SHOW_ELEMENTS),
            __('Show Elements?', \CoMa\PLUGIN_NAME),
            '\CoMa\Helper\InstallOptions::renderField',
            \CoMa\Helper\InstallOptions\GROUP_GENERAL,
            \CoMa\Helper\InstallOptions\SECTION_DEBUG, [
                'type' => 'checkbox',
                'name' => Base::getPrefixedName(\CoMa\WP\Options\DEBUG_SHOW_ELEMENTS),
                'desc' => '<code>Highlighting section, article, h1, h2, h3, h4, h5, h6, header, footer</code>'
            ]
        );
        add_settings_field(
            Base::getPrefixedName(\CoMa\WP\Options\DEBUG_LIVERELOAD),
            __('Livereload?', \CoMa\PLUGIN_NAME),
            '\CoMa\Helper\InstallOptions::renderField',
            \CoMa\Helper\InstallOptions\GROUP_GENERAL,
            \CoMa\Helper\InstallOptions\SECTION_DEBUG, [
                'type' => 'checkbox',
                'name' => Base::getPrefixedName(\CoMa\WP\Options\DEBUG_LIVERELOAD),
                'desc' => '<code>Livereload for CSS, JS, etc.</code>'
            ]
        );

    }

    public static function renderField($args)
    {

        $type = $args['type'];
        $name = $args['name'];

        $desc = '';
        if ($args['desc']) $desc = $args['desc'];
        $placeholder = '';
        if ($args['placeholder']) $placeholder = $args['placeholder'];

        $title = '';
        if ($args['title']) $title = $args['title'];

        $checked = null;
        if ($args['checked']) $checked = $args['checked'];

        $value = null;
        if ($args['value']) $value = $args['value'];


        if ($desc) {
            echo '<label for="' . $name . '">';
        }

        $attributes = ['name' => $name, 'id' => $name];

        if ($type == 'select') {

            $size = 1;
            if ($args['size']) {
                $size = $args['size'];
            }

            ?>

            <select<?php echo Base::renderTagAttributes(['name' => $name, 'id' => $name, 'size' => $size, 'class' => 'attachments_quality']); ?>><?php
                foreach ($args['options'] as $value => $title) {
                    echo '<option value="' . $value . '"' . ($value == $args['select'] ? ' selected="selected"' : '') . '>' . $title . '%</option>';
                }
                ?></select>

            <?php

        } else if (in_array($type, ['text', 'number', 'checkbox', 'radio', 'submit'])) {
            if ($type == 'checkbox' || $type == 'radio') {
                $attributes = array_merge($attributes, ['type' => $type, 'value' => ($value ? $value : 1)]);
                if ($checked == null) {
                    $checked = get_option($name);
                }
                if ($checked) {
                    $attributes['checked'] = 'checked';
                }
                echo '<input' . Base::renderTagAttributes($attributes) . ' >';
            } else if ($type == 'text' || $type == 'number') {
                if (!$value) {
                    $value = get_option($name);
                }
                $attributes = array_merge($attributes, ['class' => 'regular-text', 'type' => $type, 'value' => $value, 'placeholder' => $placeholder]);
                echo '<input' . Base::renderTagAttributes($attributes) . ' >';
            } else if ($type == 'submit') {
                submit_button($title, 'primary', $name, false);
            } else {
                $attributes = array_merge($attributes, ['class' => 'regular-text', 'type' => $type]);
                echo '<input' . Base::renderTagAttributes($attributes) . ' >';
            }
        }


        if ($desc) {
            echo '<p class="description" id="description">' . $desc . '</p>';
            echo '</label>';
        }

    }

}