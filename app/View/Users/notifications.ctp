<h2><?php echo __("Notifications"); ?></h2>

<button name="markAsRead"><?php echo __("Mark all as read"); ?></button>

<ul class="notifications">
    
    <?php foreach($notifications as $notification) : ?>
    
    <li class="notification <?php echo $notification["Notifications"]["is_viewed"] ? "read" : "new"; ?> <?php echo $notification["Notifications"]["type"]; ?>"> 
        <p><?php echo date("Y/m/d", $notification["Notifications"]["created"]); ?></p>
        <p><?php echo $notification["Notifications"]["title"]; ?></p>
        
        <?php if(array_key_exists("Achievement", $notification["Notifications"])) : ?>
            <p><?php echo $notification["Notifications"]["Achievement"]["name"]; ?></p>            
            <p><?php echo $notification["Notifications"]["Achievement"]["description"]; ?></p>
        <?php endif;?>        
    </li>
    <?php endforeach; ?>
    
</ul>
    
<?php echo $this->Paginator->numbers(); ?>
<?php echo __("Page"); ?> <?php echo $this->Paginator->counter(); ?>

<script>
    $(function(){
        $("button[name=markAsRead]").click(function(){
            $.getJSON("<?php echo Router::url(array("controller" => "users", "action" => "okstfu")); ?>", function(){
                $('.notifications .notification.new').removeClass("new").addClass("read");
            });
        });
    });
</script>