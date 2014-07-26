<!DOCTYPE html>
<html>
<head>
    <title><?php echo $this->App->getTitle($title_for_layout); ?></title>
    <?php
        $this->MetaTags->init();
        if(isset($meta_for_layout)) $this->MetaTags->addLayoutMeta($meta_for_layout);
        if(isset($preferredPlayer)) $this->MetaTags->addPlayerMeta($preferredPlayer);
        if(isset($oembedLink))      $this->MetaTags->addOEmbedMeta($oembedLink);
        if(isset($customMetas))     $this->MetaTags->add($customMetas);
        echo $this->MetaTags->compile();
     ?>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="<?php echo $this->App->contextToClassNames(); ?>" style="padding-top:0;">
    <?php echo $this->fetch('content'); ?>
    <?php echo $this->element('analytics'); ?>
    <?php echo $this->element('sql_dump'); ?>
</body>
</html>

