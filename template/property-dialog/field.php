<?php
/**
 * @type CoMa\Base\PropertyDialog\Field $this
 */

$classes = [];


if ($this->getType() == 'radio') {
    $classes[] = 'select-wrapper';
}

?>
<div class="partial <?php echo implode(' ', $classes); ?>"
     data-partial="coma/assetboard/property-dialog/field-row">
    <?php

    if ($this->getType() == 'radio') {
        echo $node;
    }

    if ($this->hasLabel()) {
        ?>
        <label for="<?php echo $this->getId(); ?>"><?php echo $this->getTitle(); ?></label>
        <?php
    }

    if ($this->getType() != 'radio') {
        echo $node;
    }

    if ($this->getDescription()) {
        ?>
        <p class="description"><?php echo $this->getDescription(); ?></p>
        <?php
    }

    ?>
</div>
