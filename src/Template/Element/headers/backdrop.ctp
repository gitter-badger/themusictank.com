<?php if (isset($entity)) : ?>
    <div class="header-wrapper">
        <div class="blurred"></div>
        <div class="clean"></div>
        <i class="mask"></i>
    </div>

    <?php $this->start('header-extra'); ?>
    <?= $this->fetch('header-extra'); ?>
    <style type="text/css">
        @media (min-width: 501px) {
            .blurred { background-image: url(<?= $entity->getImageUrl("blur"); ?>) }
            .clean { background-image: url(<?= $entity->getImageUrl("big"); ?>) }
        }
        @media (max-width: 500px) {
            .blurred { background-image: url(<?= $entity->getImageUrl("mobile_blur"); ?>) }
            .clean { background-image: url(<?= $entity->getImageUrl("mobile_big"); ?>) }
        }
    </style>
    <?php $this->end(); ?>
<?php else : ?>
    <div class="header-wrapper plain">
        <i class="mask"></i>
    </div>
<?php endif; ?>
