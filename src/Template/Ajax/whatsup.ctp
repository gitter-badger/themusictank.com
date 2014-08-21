<ul>
    <?php if(count($notifications)) : ?>
        <?php foreach($notifications as $notification) : ?>
            <li class="<?= $notification->is_viewed ? "read" : "unread" ?> <?= $notification->type ?>"><?= $notification->title ?></li>
        <?php endforeach; ?>
    <?php else : ?>
        <li class="no-notifications"><?= __("You currently have no notifications"); ?></li>
    <?php endif; ?>
</ul>
