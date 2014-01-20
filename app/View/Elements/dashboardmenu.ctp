<nav class="dashboard">
    <ul class="horizontal">
        <li><?php echo $this->Html->link(__("Dashboard home"), array('controller' => 'users', 'action' => 'dashboard')); ?></li>
        <li><?php echo $this->Html->link(__("Notifications"), array('controller' => 'users', 'action' => 'notifications')); ?> </li>
        <li><?php echo $this->Html->link(__("Achievements"), array('controller' => 'users', 'action' => 'achievements')); ?> </li>
        <li><?php echo $this->Html->link(__("Your subscribtions"), array('controller' => 'users', 'action' => 'following')); ?> </li>
        <li><?php echo $this->Html->link(__("Youre followers"), array('controller' => 'users', 'action' => 'followers')); ?> </li>
        <li><?php echo $this->Html->link(__("Edit account"), array('controller' => 'users', 'action' => 'edit')); ?></li>
    </ul>
</nav>