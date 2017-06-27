@if (!is_null(auth()->user()))
    Tmt.app.user(<?php echo auth()->user()->toJson() ?>);
    Tmt.app.activities(<?php echo auth()->user()->activities->toJson() ?>);
    Tmt.app.subscriptions(<?php echo auth()->user()->subscriptions->toJson() ?>);
    Tmt.app.upvotes({
        'albumUpvotes' : <?php echo auth()->user()->albumUpvotes->toJson() ?>,
        'trackUpvotes' : <?php echo auth()->user()->trackUpvotes->toJson() ?>
    });
@endif
