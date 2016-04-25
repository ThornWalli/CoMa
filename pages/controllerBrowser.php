<div class="wrap">
    <h2><?php echo __(\CoMa\PLUGIN_TITLE_SHORT, \CoMa\PLUGIN_NAME) . ' - ' . __('Controller-Browser', \CoMa\PLUGIN_NAME); ?></h2>
</div>

<div>

    <div class="coma-controller partial" data-coma-controller="components/ControllerBrowser"
         data-partial="coma/component/controller-browser"<?php
    echo CoMa\Helper\Base::renderTagAttributes([
        'ajax' => \CoMa\ADMIN_URL,
        'can-disable' => \CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_DISABLE) ? 'true' : 'false'
    ], 'data');
    ?><?php
    echo CoMa\Helper\Base::renderTagAttributes([
        'delete-property' => __('properties delete?', \CoMa\PLUGIN_NAME),
    ], 'lang');
    ?>>

        <script id="property-template" type="text/template">

            {{

            _.each(properties, function (value, key) {

            }}

            <div class="property">
                <input type="checkbox" name="propertyRemove" value="{{= key }}">

                <div>
                    <div class="title">
                        <input name="propertyName" data-last-value="{{= key }}" type="text" placeholder="empty"
                               value="{{- key }}"/>
                    </div>
                    <div class="value">
                        {{

                        if (typeof value == 'object') {

                        }}<textarea name="{{= key }}" data-last-value="{{= key }}" type="text" placeholder="empty">{{= JSON.stringify(value) }}</textarea>{{

                        } else {

                        }}<input name="{{= key }}" data-last-value="{{= key }}" type="text" placeholder="empty"
                                 value="{{= value }}"/>{{

                        }

                        }}

                    </div>

                </div>
            </div>

            {{

            });

            }}

        </script>
        <script id="controller-template" type="text/template">

            {{

            controllers.forEach(function (controller) {

            var className = controller['class'].replace(/\\\\/g, "\\");

            }}

            <div class="coma-controller partial" data-coma-controller="components/controllerBrowser/Controller"
                 data-partial="coma/component/controller-browser/controller" data-id="{{= controller.id }}"
                 data-ajax="{{= ajax }}"
                 data-target=".partial[data-partial='coma/component/controller-browser']">

                <input type="checkbox" name="controllerRemove" value="{{= controller.id }}">

                {{

                if (this.canDisable) {

                }}

                <select name="controllerDisabled">
                    <option value="0"
                            {{ if (controller.disabled== 0) { }}selected{{}
                            }}><?php echo __('Activated', \CoMa\PLUGIN_NAME); ?></option>
                    <option value="1"
                            {{ if (controller.disabled== 1) { }} selected{{}
                            }}><?php echo __('Deactivated', \CoMa\PLUGIN_NAME); ?></option>
                </select>

                {{

                }

                }}

                <span class="title">{{= className }} [{{= controller.position }}]</span>

                <div class="properties"></div>
                <div class="controllers"></div>

            </div>

            {{

            },{ 'canDisable':canDisable });

            }}

        </script>

        <form method="post" action="">
            <div class="controls">
                <?php

                $pageId = null;
                if (isset($_POST['pageId'])) {
                    $pageId = $_POST['pageId'];
                }

                ?>

                <input type="checkbox" name="toggleChecked">

                <input type="button" class="button button-primary" name="removeSelected"
                       value="<?php echo __('Remove'); ?>"/>
                <?php

                if (isset($_POST['entryType']) && $_POST['entryType'] == 'static') {
                    $pageId = 0;
                    ?>
                    <input type="hidden" name="pageId" id="pageId" value="0"/>
                    <?php
                } else {

                    ?>
                    <label
                        for="pageId"><?php echo __((isset($_POST['entryType']) && $_POST['entryType'] == 'post' ? 'Posts' : 'Pages')) . ':'; ?></label>
                    <?php

                    if (isset($_POST['entryType']) && $_POST['entryType'] == 'post') {
                        ?>
                        <select name="pageId"
                                id="pageId"> <?php
                            global $post;
                            $args = ['numberposts' => -1];
                            $posts = get_posts($args);
                            foreach ($posts as $post) {

                                setup_postdata($post);
                                echo '<option value="' . $post->ID . '">' . get_the_title() . '</option>';

                            }

                            ?></select>
                        <?php
                    } else {
                        wp_dropdown_pages(['sort_column' => 'menu_order', 'name' => 'pageId', 'selected' => $pageId]);
                    }
                }


                ?>


                <div class="type">
                    <label for="entryType"><?php echo __('Show Posts:', \CoMa\PLUGIN_NAME); ?></label>
                    <select name="entryType" id="entryType">
                        <option value="page"<?php if (isset($_POST['entryType']) && $_POST['entryType'] == 'page') {
                            echo ' selected';
                        } ?>><?php echo __('Pages'); ?></option>
                        <option value="post"<?php if (isset($_POST['entryType']) && $_POST['entryType'] == 'post') {
                            echo ' selected';
                        } ?>><?php echo __('Posts'); ?></option>
                        <option value="static"<?php if (isset($_POST['entryType']) && $_POST['entryType'] == 'static') {
                            echo ' selected';
                        } ?>><?php echo __('Static', \CoMa\PLUGIN_NAME); ?></option>
                    </select>
                </div>

            </div>
        </form>

        <div class="controllers"></div>

    </div>


</div>