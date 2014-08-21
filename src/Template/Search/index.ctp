<nav class="sub-menu">
    <div class="container container-fluid">
        <div class="row">
            <ol class="breadcrumb">
                <li class="active"><?= $this->Html->link(__("Search"), ['controller' => 'search', 'action' => 'index']); ?></li>
            </ol>
        </div>
    </div>
</nav>
<div class="container container-fluid">

    <h2><?= __("Search Results"); ?></h2>

    <h3><?= __("Artists"); ?></h3>
    <?php if(count($artists)): ?>

        <?php echo $this->element('artistTiledList', ["artists" => array_slice($artists, 0, $maxResults)]); ?>

        <?php if(count($artists) >= $maxResults) : ?>
            <?= $this->Html->link(__("View more"), ['controller' => 'artists', 'action' => 'search', '?' => ["name" => $query]]); ?>
        <?php endif; ?>

    <?php else : ?>
        <p><?=__("Search returned no results."); ?></p>
    <?php endif; ?>

    <h3><?php echo __("Ablums"); ?></h3>
    <?php if(count($albums)): ?>
        <?= $this->element('albumTiledList', array("albums" => array_slice($albums, 0, $maxResults))); ?>

        <?php if(count($albums) >= $maxResults) : ?>
            <?= $this->Html->link(__("View more"), ['controller' => 'albums', 'action' => 'search', '?' => ["name" => $query]]); ?>
        <?php endif; ?>

    <?php else : ?>
        <p><?= __("Search returned no results."); ?></p>
    <?php endif; ?>

    <h3><?= __("Tracks"); ?></h3>
    <?php if(count($tracks) > 0): ?>
        <?= $this->element('trackTiledList', ["tracks" => array_slice($tracks, 0, $maxResults)]); ?>

        <?php if(count($albums) >= $maxResults) : ?>
            <?= $this->Html->link(__("View more"), ['controller' => 'tracks', 'action' => 'search', '?' => ["name" => $query]]); ?>
        <?php endif; ?>

    <?php else : ?>
        <p><?= __("Search returned no results."); ?></p>
    <?php endif; ?>

</div>
