<!DOCTYPE html>
<html>
<head>
    <title><?= $this->fetch('title') ?></title>
    <?php
        $this->MetaTags->init();
        if(isset($meta_for_layout)) $this->MetaTags->addLayoutMeta($meta_for_layout);
        if(isset($oembedLink))      $this->MetaTags->addOEmbedMeta($oembedLink);
        if(isset($customMetas))     $this->MetaTags->add($customMetas);
    ?>
    <?= $this->MetaTags->compileMetas();  ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>
<body class="<?= $this->Tmt->contextToClassNames(); ?>">
    <?php echo $this->fetch('content'); ?>
    <?= $this->MetaTags->compileScripts(); ?>
    <?= $this->fetch('script') ?>
    <?= $this->element('analytics'); ?>
</body>
</html>

