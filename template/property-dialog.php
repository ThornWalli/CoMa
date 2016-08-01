<?php
/**
 * @type \CoMa\Base\PropertyDialog $this
 * @type \CoMa\Base\PropertyDialog\Tab $tab
 */
?>

<h2><?php echo $this->getTitle(); ?></h2>

<div class="content">

  <div class="partial wp-core-ui" data-partial="coma/assetboard/property-dialog">


    <div class="partial coma-controller" data-coma-controller="components/TabContainer"
         data-partial="coma/component/tab-container">

      <?php

      $hasTabs = false;

      foreach ($this->tabs as $tab) {
        if ($tab->hasFields()) {
          $tabsHasFields++;
        }
      }

      if ($tabsHasFields > 1) {
        echo "<ul>";
      } else {
        echo "<ul style=\"display: none;\">";
      }


      foreach ($this->tabs as $tab) {
        if ($tab->hasFields()) {
          $title = $tab->getTitle();
          if (!$title) {
            $title = $tab->getName();
          }
          echo '<li><a href="#' . $tab->getName() . '" title="' . $title . '">' . $title . '</a></li>';
        }
      }

      ?>

      </ul>

      <section>
        <?php

        foreach ($this->tabs as $tab) {
          if ($tab->hasFields()) {
            echo '<article data-tab="#' . $tab->getName() . '">';
            $tab->render($properties);
            echo '</article>';
          }
        }

        ?>
      </section>

    </div>

    <div class="buttons">
      <?php

      foreach ($this->buttons as $button) {
        /**
         * @type \CoMa\Base\PropertyDialog\Button $button
         */
        $button->render();
      }

      ?>
    </div>
  </div>

</div>
