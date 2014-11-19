<?= $this->element('headers/backdrop', ['entity' => $featuredArtist]); ?>

<article class="container container-fluid popular-artists">

    <?php if(!is_null($featuredArtist)) : ?>
        <header class="featured">

            <span><?php echo __("Featured artist"); ?></span>
            <h1><?= $this->Html->link($featuredArtist->name, ['controller' => 'artists', 'action' => 'view', $featuredArtist->slug]); ?></h1>

            <div class="everyone piechart"></div>

            <section class="row stats tiles albums">
                <?php if(count($featuredArtist->albums)) : ?>
                    <?php foreach($featuredArtist->albums as $i => $album) : if($i >= 4) break;?>
                    <div class="col-xs-3 col-md-3 album">

                        <?= $this->Html->link($this->Html->image($album->getImageUrl(), ['alt' => $album->name]), ['controller' => 'albums', 'action' => 'view', $album->slug], ['escape' => false, 'class' => "thumbnail"]); ?>

                        <h3><?= $this->Html->link($album->name, ['controller' => 'albums', 'action' => 'view', $album->slug]); ?></h3>

                        <?php if($album->hasReleaseDate()) : ?>
                        <time datetime="<?= h($album->getFormatedReleaseDate("c")); ?>"><?= h($album->getFormatedReleaseDate()); ?></time>
                        <?php endif; ?>

                        <div class="score">
                            <?php if(is_null($featuredArtist->snapshot)) : ?>
                                N/A
                            <?php else : ?>
                                <?= (int)($featuredArtist->snapshot->score * 100); ?>%
                            <?php endif; ?>
                            <span><?= __("Score"); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($featuredArtist->albums) > 4) : ?>
                        <?= $this->Html->link(__("View more"), ['controller' => 'artists', 'action' => 'discography', $featuredArtist->slug], ['class' => "btn btn-primary pull-right"]); ?>
                    <?php endif; ?>
                <?php else : ?>
                    <?= __("We did not fetch all the albums yet."); ?>
                    <?= $this->Html->link(__("View"), ['controller' => 'artists', 'action' => 'discography', $featuredArtist->slug], ['class' => "btn btn-primary pull-right"]); ?>
                <?php endif; ?>
            </section>
        </header>
    <?php endif; ?>

    <div class="row content">
        <section class="col-xs-12 col-md-12 more-popular-artists">
            <h2><?= __('Also popular right now'); ?></h2>
            <?= $this->element('tiledlists/artists', ['artists' => $popularArtists]); ?>
        </section>
    </div>

    <div class="row content">
        <section class="col-xs-12 col-md-6 search-everything">
            <p class="lead"><?= __("Search for an artist, an album title or a track name."); ?></p>
            <form action="/search/" method="get">
                <input class="typeahead" type="text" name="q" value="" placeholder="Search across everything" />
                <input type="submit" />
            </form>
        </section>

        <section class="col-xs-12 col-md-6 browse-by-letter">
            <p class="lead"><?= __("Browse our list list of artists alphabetically."); ?></p>
            <?php foreach($artistCategories as $category) : ?>
                <div class="col-xs-1 col-md-2">
                    <?= $this->Html->link($category, array('controller' => 'artists', 'action' => 'browse',  strtolower($category))); ?>
                </div>
            <?php endforeach; ?>
        </section>

    </div>

    <div class="row content">
        <section class="new-album-releases">
            <h2><?= __('Recent releases'); ?></h2>
            <p class="lead"><?= __("Check out the newest additions to our catalog."); ?></p>
            <?= $this->element('tiledlists/albums', ['albums' => $newReleases]); ?>
            <?= $this->Html->link(__("View more"), ['controller' => 'albums', 'action' => 'newreleases'], ['class' => "btn btn-primary pull-right"]); ?>
        </section>
    </div>

</article>

<?php $this->start('bottom-extra'); ?>
<?php if(isset($featuredArtist->snapshot)) : ?>
    <script>$(function(){
        tmt.createPie(".everyone.piechart",
            [
                {"type" : "smile", "value" : <?php echo $featuredArtist->snapshot->liking_pct; ?>},
                {"type" : "meh", "value" : <?php echo $featuredArtist->snapshot->neutral_pct; ?>},
                {"type" : "frown", "value" : <?php echo $featuredArtist->snapshot->disliking_pct; ?>}
            ],
            {key: "tanker chart-tanker"});
    });</script>
<?php endif; ?>
<?php $this->end(); ?>
