<nav class="charts">
    <ul class="horizontal">
        <li><?php echo $this->Html->link(__("Charts overview"), array('controller' => 'charts', 'action' => 'index')); ?></li>
        <li><?php echo __("Weekly"); ?></li>
        <li><?php echo $this->Html->link(__("Top 100 albums"), array('controller' => 'charts', 'action' => 'weekly', 'albums')); ?> </li>
        <li><?php echo $this->Html->link(__("Top 100 tracks"), array('controller' => 'charts', 'action' => 'weekly', 'tracks')); ?> </li>
        <li><?php echo __("Monthly"); ?></li>
        <li><?php echo $this->Html->link(__("Top 100 albums"), array('controller' => 'charts', 'action' => 'monthly', 'albums')); ?> </li>
        <li><?php echo $this->Html->link(__("Top 100 tracks"), array('controller' => 'charts', 'action' => 'monthly', 'tracks')); ?> </li>
        <li><?php echo __("Yearly"); ?></li>
        <li><?php echo $this->Html->link(__("Top 100 albums"), array('controller' => 'charts', 'action' => 'yearly', 'albums')); ?> </li>
        <li><?php echo $this->Html->link(__("Top 100 tracks"), array('controller' => 'charts', 'action' => 'yearly', 'tracks')); ?> </li>
        
        <li><?php echo $this->Html->link(__("Search"), array('controller' => 'charts', 'action' => 'search')); ?> </li>
    </ul>
</nav>