<h2><?php echo __("Notifications"); ?></h2>

<button name="markAsRead"><?php echo __("Mark all as read"); ?></button>

<ul class="notifications">    
    <?php foreach($notifications as $notification) : ?>
        <li class="notification <?php echo $notification["Notifications"]["is_viewed"] ? "read" : "new"; ?> <?php echo $notification["Notifications"]["type"]; ?>"> 

            <time datetime="<?php echo $this->Time->i18nFormat($notification["Notifications"]["created"]); ?>" data-title="true" title="<?php echo $this->Time->niceShort($notification["Notifications"]["created"]); ?>">
                <?php echo $this->Time->timeAgoInWords($notification["Notifications"]["created"], array('accuracy' => array('day' => 'day'), 'end' => '1 month')); ?>
            </time>

            <p><?php echo $notification["Notifications"]["title"]; ?></p>

            <?php if(array_key_exists("Achievement", $notification["Notifications"])) : ?>
                <p><?php echo $notification["Notifications"]["Achievement"]["name"]; ?></p>            
                <p><?php echo $notification["Notifications"]["Achievement"]["description"]; ?></p>            

            <?php elseif(array_key_exists("UserFollower", $notification["Notifications"])) : ?>            
                <p>
                    <?php echo $this->Html->link($notification["Notifications"]["UserFollower"]["firstname"] . " " . $notification["Notifications"]["UserFollower"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $notification["Notifications"]["UserFollower"]["slug"])); ?>
                    <?php echo __("is now following your public activity on the website."); ?>
                </p>
            <?php endif; ?>        
        </li>
    <?php endforeach; ?>    
</ul>
    
<?php echo $this->Paginator->numbers(); ?>
<?php echo __("Page"); ?> <?php echo $this->Paginator->counter(); ?>

<script>
    $(function(){
        $("button[name=markAsRead]").click(function(){
            $.getJSON("<?php echo Router::url(array("controller" => "ajax", "action" => "okstfu")); ?>", function(){
                $('.notifications .notification.new').removeClass("new").addClass("read");
            }); 
        });
    });
</script>