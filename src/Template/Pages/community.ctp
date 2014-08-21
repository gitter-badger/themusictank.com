<div class="header-wrapper plain">
    <i class="mask"></i>
</div>

<article class="container container-fluid static">

    <header class="collapsed"></header>

    <div class="row content headerless">
        <h1><?= __("Community hub"); ?></h1>
        <div class="col-md-12">
            <?= $this->element('disqus', [
                'identifier'    => '/pages/community/',
                'title'         => __("Community")
            ]); ?>
        </div>
    </div>

</article>
