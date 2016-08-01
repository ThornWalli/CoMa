<?php
/**
 * @type CoMa\Base\ThemeComponent $this
 */


if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT)) {

  $dataAttributes = [
    'ajax' => \CoMa\ADMIN_URL,
    'id' => $this->getId(),
    'rank' => $this->getRank(),
    'parent-id' => $this->getParentId(),
    'deep-modal' => \CoMa\DEEP_MODAL,
    'disabled' => $this->getDisabled(),
    'template-name' => $this::TEMPLATE_NAME
  ];

  $dataAttributes['target'] = "[data-partial='coma/component/controller/area'][data-id='" . $this->getParentId() . "']";

  ?>

  <div class="partial coma-controller" data-coma-controller="components/controller/Component"
       data-partial="coma/component/controller/component"<?php
  echo CoMa\Helper\Base::renderTagAttributes($dataAttributes, 'data');
  ?><?php
  echo CoMa\Helper\Base::renderTagAttributes([
    'delete' => __('Delete Component?', \CoMa\PLUGIN_NAME),
    'activate' => __('Activate Component?', \CoMa\PLUGIN_NAME),
    'deactivate' => __('Deactivate Component?', \CoMa\PLUGIN_NAME)
  ], 'lang');
  ?>>

    <div class="header">
      <div>
        <span class="name"><?php echo $this::TEMPLATE_NAME; ?></span>
        <ul>
          <?php

          if ($this->getControl('copy')) {
            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_COPY)) {

              ?>
              <li class="separator"></li>
              <li>
                <a class="copy dashicons dashicons-admin-page" href="#copy"
                   title="<?php echo __('Copy', \CoMa\PLUGIN_NAME); ?>"></a>
              </li>
              <?php

            }
          }

          if ($this->getControl('move')) {
            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_MOVE)) {

              ?>
              <li class="separator"></li>
              <li>
                <a class="move dashicons dashicons-sort" href="#move"
                   title="<?php echo __('Move', \CoMa\PLUGIN_NAME); ?>"></a>
              </li>
              <?php

            }
          }

          if ($this->getControl('rank_up')) {
            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_RANK_UP)) {

              ?>
              <li class="separator"></li>
              <li>
                <a class="up dashicons dashicons-arrow-up-alt2" href="#up"
                   title="<?php echo __('Up', \CoMa\PLUGIN_NAME); ?>"></a>
              </li>
              <?php

            }
          }

          if ($this->getControl('rank_down')) {
            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_RANK_DOWN)) {

              ?>
              <li class="separator"></li>
              <li>
                <a class="down dashicons dashicons-arrow-down-alt2" href="#down"
                   title="<?php echo __('Down', \CoMa\PLUGIN_NAME); ?>"></a>
              </li>
              <?php
            }
          }

          if ($this->getControl('edit')) {
            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_EDIT)) {

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

          if ($this->getControl('remove')) {
            if (\CoMa\Helper\Base::roleHasCap(\CoMa\Roles\COMPONENT_REMOVE)) {

              ?>
              <li class="separator"></li>
              <li class="item">
                <a class="remove dashicons dashicons-trash" href="#remove"
                   title="<?php echo __('Delete', \CoMa\PLUGIN_NAME); ?>"></a>
              </li>
              <?php

            }
          }

          ?>
        </ul>
      </div>
    </div>

    <div class="helper"
         data-template-name="<?php echo $this::TEMPLATE_NAME; ?>"><?php echo __('Component', \CoMa\PLUGIN_NAME) ?> </div>

    <div class="content">

      <?php

      if (array_key_exists('html', $options) && $options['html']) { ?>
     <!--
        includePath: <?php echo $includePath; ?>
        -->

        <?php
        echo $options['html'];
      } else {
        include($options['path']);
      }

      ?>

    </div>

  </div>

  <?php

} else {

  include($includePath);

}

?>
