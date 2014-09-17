
<form action="/ajax/bugreport" role="form">
    <?php if(isset($bug)) : ?>
        <input type="hidden" name="id" value="<?php echo $bug->id; ?>" />
    <?php endif; ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo __("Close"); ?></span></button>
        <h4 class="modal-title"><?php echo __("Issue Report"); ?></h4>
    </div>
    <div class="modal-body">
        <p class="lead"><?php echo __("I have been notified there was a problem with this area and I will fix it as soon as I can."); ?></p>
        <p class="lead"><?php echo __("Thanks again for helping keep the Tank healthy!"); ?> <i class="fa fa-heart"></i></p>
        <p><?php echo __("Did you want to add additional information?"); ?></p>
        <textarea class="form-control" name="details"></textarea>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __("Close"); ?></button>
    </div>
</form>
