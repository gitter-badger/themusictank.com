<?php
    if(!is_null($letter)) {
        $this->Paginator->options(["url" => [$letter]]);
    }
?>
<?= $this->element('breadcrumbs', ['links' => [
        $this->Html->link(__("Artists"), ['controller' => 'artists', 'action' => 'index']),
        $title,
    ]]);
?>

<div class="header-wrapper plain">
    <i class="mask"></i>
</div>

<article class="container container-fluid static">

    <header class="collapsed"></header>

    <div class="row content headerless">

        <h1><?= __("Artist search"); ?></h1>
        <h3><?= $title; ?></h3>

        <?php if(count($artists) > 0): ?>
            <?= $this->element('tiledlists/artists', ['artists' => $artists]); ?>
        <?php else : ?>
            <p><?=__("Search returned no results."); ?></p>
        <?php endif; ?>

         <ul class="pagination">
            <?php
                echo $this->Paginator->prev(__('prev'), ['tag' => 'li'], null, ['tag' => 'li','class' => 'disabled','disabledTag' => 'a']);
                echo $this->Paginator->numbers(['separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1]);
                echo $this->Paginator->next(__('next'), ['tag' => 'li','currentClass' => 'disabled'], null, ['tag' => 'li','class' => 'disabled','disabledTag' => 'a']);
            ?>
        </ul>
    </div>
</article>
