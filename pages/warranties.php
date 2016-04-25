<div class="wrap">
    <h2><?php echo __(\CoMa\PLUGIN_TITLE_SHORT, \CoMa\PLUGIN_NAME) . ' - ' . __('Roles', \CoMa\PLUGIN_NAME); ?></h2>
</div>

<div>

    <div class="coma-controller partial" data-coma-controller="components/admin/Warranties"
         data-partial="coma/component/warranties"<?php echo CoMa\Helper\Base::renderTagAttributes(['ajax' => \CoMa\ADMIN_URL], 'data'); ?>>

        <script id="capability-template" type="text/template">
            {{

                _.each(roles, function (role) {

            }}

            <li><input type="checkbox" id="{{= role.name }}" name="{{= role.name }}" value="{{= role.name }}" {{ if
                       (role.checked) { }}checked {{ } }}/><label
                    for="{{= role.name }}"><span>{{= role.name }}</span></label></li>

            {{

                });

            }}

        </script>

        <div class="controls">

            <input type="checkbox" name="toggleChecked"/>
            <label for="role"><?php echo __('Role', \CoMa\PLUGIN_NAME); ?>:</label>
            <select id="role" name="role"><?php

                foreach (\CoMa\Helper\Base::getEditableRoles() as $key => $role) {
                    ?>
                    <option
                    value="<?php echo $key; ?>"><?php echo __($role['name'], \CoMa\PLUGIN_NAME); ?></option><?php
                }

                ?></select>
            <label for="filter"><?php echo __('Filter', \CoMa\PLUGIN_NAME); ?>:</label>
            <select id="filter" name="filter">
                <option value=""><?php echo __('Content-Manager Roles', \CoMa\PLUGIN_NAME); ?></option>
                <option value="all"><?php echo __('All Roles', \CoMa\PLUGIN_NAME); ?></option>
            </select>

        </div>


        <div class="capabilities">
            <ul></ul>
        </div>

        <div>

            <input type="button" class="button button-primary" name="save"
                   value="<?php echo __('Save', \CoMa\PLUGIN_NAME); ?>"/>

        </div>

    </div>


</div>