<nav class="dashboard">
    <ul class="horizontal">
        <li><?php echo $this->Html->link($user["firstname"] . " " . $user["lastname"], array('controller' => 'profiles', 'action' => 'view', $user["slug"])); ?> </li>
        <li><?php echo $this->Html->link(__("Achievements"), array('controller' => 'profiles', 'action' => 'achievements', $user["slug"])); ?> </li>
        <li><?php echo $this->Html->link(__("Subscribtions"), array('controller' => 'profiles', 'action' => 'subscriptions', $user["slug"])); ?></li>
        <li><?php echo $this->Html->link(__("Followers"), array('controller' => 'profiles', 'action' => 'followers', $user["slug"])); ?> </li>
    </ul>
</nav>