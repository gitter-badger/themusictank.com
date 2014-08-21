<?php
    use Cake\Routing\Router;
    use App\Model\Entity\Notification;
?>
<div class="header-wrapper plain">
    <i class="mask"></i>
</div>

<article class="container container-fluid static">

    <header class="collapsed"></header>

    <div class="row content headerless">

        <h1><?= __("Notifications"); ?></h1>

        <button name="markAsRead"  class="btn btn-primary"><?= __("Mark all as read"); ?></button>

        <div class="notifications">
            <?php if(count($notifications)): ?>
                <?php foreach($notifications as $notification) : ?>
                    <div class="notification <?= $notification->is_viewed ? "read" : "new"; ?> <?= $notification->type; ?>">

                        <h3><?= $notification->title; ?></h3>

                        <time datetime="<?= $notification->created->format(DATE_RFC850); ?>" data-title="true" title="<?= $notification->created->format(DATE_RFC850); ?>">
                            <?= $notification->created->format(DATE_RFC850); ?>
                        </time>

                        <?php if($notification->isLinkedToObject()) : ?>
                            <?php $linkedObject = $notification->getLinkedObject(); ?>
                            <?php if($linkedObject) : ?>
                                <?php if($notification->type === Notification::TYPE_ACHIEVEMENT) : ?>

                                    <p><blockquote><?= $linkedObject->description; ?></blockquote></p>

                                <?php elseif($notification->type === Notification::TYPE_FOLLOWER) : ?>
                                    <p>
                                        <?= $this->Html->link($linkedObject->username, ['controller' => 'profiles', 'action' => 'view', $linkedObject->slug]); ?>
                                        <?= __("is now following you!"); ?>
                                    </p>

                                <?php elseif($notification->type === Notification::TYPE_BUG) : ?>
                                    <p><?= __("There's a new bug dude."); ?></p>
                                <?php endif; ?>

                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p><?= __("You have no notifications at the moment."); ?></p>
            <?php endif; ?>
        </div>

        <ul class="pagination">
            <?php
                echo $this->Paginator->prev(__('prev'), ['tag' => 'li'], null, ['tag' => 'li','class' => 'disabled','disabledTag' => 'a']);
                echo $this->Paginator->numbers(['separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1]);
                echo $this->Paginator->next(__('next'), ['tag' => 'li','currentClass' => 'disabled'], null, ['tag' => 'li','class' => 'disabled','disabledTag' => 'a']);
            ?>
        </ul>

    </div>
</article>


<?php $this->start('bottom-extra'); ?>
<script>$(function(){
        $("button[name=markAsRead]").click(function(){
            $.getJSON("<?= Router::url(["controller" => "ajax", "action" => "okstfu"]); ?>", function(){
                $('.notifications .notification.new').removeClass("new").addClass("read");
            });
        });
    });
});</script>
<?php $this->end(); ?>
