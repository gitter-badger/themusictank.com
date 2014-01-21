<nav class="dashboard">
    <ul class="horizontal">
        <li><?php echo $this->Html->link(__("Dashboard home"), array('controller' => 'users', 'action' => 'dashboard')); ?></li>
        <li><?php echo $this->Html->link(__("Notifications"), array('controller' => 'users', 'action' => 'notifications')); ?> </li>
    </ul>
</nav>