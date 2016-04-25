<?php

/**
 * @type CoMa\Base\Area $this
 */

$position = $this->getPosition();

if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\AREA)) {

    ?>

    <!--

     id: <?php echo $this->getId(); ?>
     class: <?php echo $this->getClass(); ?>
     includePath: <?php echo $includePath; ?>

     -->

    <div class="partial coma-controller" data-coma-controller="components/controller/Area"
         data-partial="coma/component/controller/area"<?php
    echo CoMa\Helper\Base::renderTagAttributes([
        'ajax' => \CoMa\ADMIN_URL,
        'id' => $this->getId(),
        'rank' => $this->getRank(),
        'class' => $this->getClass(),
        'parent-id' => $this->getParentId(),
        'page-id' => $this->getPageId(),
        'position' => $position,
        'deep-modal' => \CoMa\DEEP_MODAL,
        'disabled' => $this->getDisabled(),
        'component-alignment' => $this::COMPONENT_ALIGNMENT,
        'target' => '.coma-controller[data-coma-controller=\'CoMa\']'
    ], 'data');
    ?><?php
    echo CoMa\Helper\Base::renderTagAttributes([
        'delete' => __('Delete Area?', \CoMa\PLUGIN_NAME),
        'activate' => __('Activate Area?', \CoMa\PLUGIN_NAME),
        'deactivate' => __('Deactivate Area?', \CoMa\PLUGIN_NAME)
    ], 'lang');
    ?>>

        <div class="header">
            <div>
                <span class="name"><?php echo $this::TEMPLATE_NAME; ?> [<?php echo $position; ?>]</span>

                <ul>
                    <?php

                    if ($this->getControl('append')) {
                        if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_SELECT) && \CoMa\Base\Controller::checkChildCount($this)) {

                            ?>

                            <li class="separator"></li>
                            <li class="item">
                                <a class="append dashicons dashicons-plus" href="#append"
                                   title="<?php echo __('Append', \CoMa\PLUGIN_NAME); ?>"></a>
                            </li>

                            <?php

                        } else {

                            ?>

                            <li class="separator"></li>
                            <li class="item">
                                <?php echo sprintf(__('Max. %d component(s)', \CoMa\PLUGIN_NAME), $this::MAX_CHILD_COUNT); ?>
                            </li>

                            <?php

                        }
                    }

                    if ($this->getControl('edit')) {
                        if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\AREA_EDIT) && $this->getPropertyDialog() != null) {

                            ?>
                            <li class="separator"></li>
                            <li class="item">
                                <a class="edit dashicons dashicons-edit" href="#edit"
                                   title="<?php echo __('Edit', \CoMa\PLUGIN_NAME); ?>"></a>
                            </li>
                            <?php

                        }
                    }

                    if ($this->getControl('disabled')) {
                        if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_DISABLE)) {

                            ?>

                            <li class="separator"></li>

                            <li class="item">
                                <a class="activate dashicons dashicons-visibility" href="#activate"
                                   title="<?php echo __('Activate', \CoMa\PLUGIN_NAME); ?>"
                                   data-disabled="0"></a>
                            </li>
                            <li class="item">
                                <a class="deactivate dashicons dashicons-hidden" href="#deactivate"
                                   title="<?php echo __('Deactivate', \CoMa\PLUGIN_NAME); ?>"
                                   data-disabled="1"></a>
                            </li>

                            <?php

                        }
                    }

                    ?>
                </ul>
            </div>
        </div>

        <div class="helper" data-position="<?php echo $position; ?>"
             data-template-name="<?php echo $this::TEMPLATE_NAME; ?>"><?php echo __('Area', \CoMa\PLUGIN_NAME) ?></div>

        <div class="content area-content"><?php

            include($includePath);

            ?></div>

        <a class="append"><span><?php echo __('Add Component', \CoMa\PLUGIN_NAME) ?></span></a>

    </div>

    <?php

} else {

    include($includePath);

}

?>