<div class="coma-controller partial" data-content-container=">.content>section>article"
     data-coma-controller="components/Modal" data-partial="coma/component/modal/property-dialog"<?php
echo CoMa\Helper\Base::renderTagAttributes([
    'deep' => 'coma-dialog',
    'has-cache' => 'false',
    'ajax' => \CoMa\ADMIN_URL . '?coma-dialog=',
    'history-method' => 'replace',
    'target' => "[data-coma-controller='CoMa']"
], 'data');
?>>
    <div class="content">
        <section>
            <article></article>
            <a class="fullscreen-toggle" href="#"></a>
            <a class="close" href="#"<?php
            echo CoMa\Helper\Base::renderTagAttributes(['deep' => \CoMa\PREFIX . '-dialog'], 'data');
            ?>></a>
        </section>
    </div>
</div>


