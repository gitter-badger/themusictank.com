<div class="container container-fluid">

    <h2><?php echo __("Notifications"); ?></h2>

    <button name="markAsRead"  class="btn btn-primary"><?php echo __("Mark all as read"); ?></button>

    <div class="notifications">    
        <?php foreach($notifications as $notification) : ?>
            <div class="notification <?php echo $notification["Notifications"]["is_viewed"] ? "read" : "new"; ?> <?php echo $notification["Notifications"]["type"]; ?>"> 

                <h3><?php echo $notification["Notifications"]["title"]; ?></h3>

                <time datetime="<?php echo $this->Time->i18nFormat($notification["Notifications"]["created"]); ?>" data-title="true" title="<?php echo $this->Time->niceShort($notification["Notifications"]["created"]); ?>">
                    <?php echo $this->Time->timeAgoInWords($notification["Notifications"]["created"], array('accuracy' => array('day' => 'day'), 'end' => '1 month')); ?>
                </time>

                <?php if(array_key_exists("Achievement", $notification["Notifications"])) : ?>
                   <p><blockquote><?php echo $notification["Notifications"]["Achievement"]["description"]; ?></blockquote></p>

                <?php elseif(array_key_exists("UserFollower", $notification["Notifications"])) : ?>            
                    <p>
                        <?php echo $this->Html->link($notification["Notifications"]["UserFollower"]["firstname"] . " " . $notification["Notifications"]["UserFollower"]["lastname"], array('controller' => 'profiles', 'action' => 'view', $notification["Notifications"]["UserFollower"]["slug"])); ?>
                        <?php echo __("is now following your public activity on the website."); ?>
                    </p>
                <?php endif; ?>        
            </div>
        <?php endforeach; ?>    
    </div>
        
    <ul class="pagination">
    <?php
        echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
        echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
        echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
    ?>
    </ul>    

    <script>
        $(function(){
            $("button[name=markAsRead]").click(function(){
                $.getJSON("<?php echo Router::url(array("controller" => "ajax", "action" => "okstfu")); ?>", function(){
                    $('.notifications .notification.new').removeClass("new").addClass("read");
                }); 
            });
        });
    </script>
</div>