@php
    $user = auth()->user();
@endphp


<script>(function(){
@if (!is_null($user))
    Tmt.app.user(<?php echo $user->toJson() ?>);
    Tmt.app.activities(<?php echo $user->activities->toJson() ?>);
    Tmt.app.subscriptions(<?php echo $user->subscriptions->toJson() ?>);
    Tmt.app.upvotes({
    'albumUpvotes' : <?php echo $user->albumUpvotes->toJson() ?>,
    'trackUpvotes' : <?php echo $user->trackUpvotes->toJson() ?>
    });
@endif
@stack('app-javascript')
})();</script>

