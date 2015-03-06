$(function() {

    // Notifier
    // --------
    //
    // Every once in a while, we check for user notifications. Also allow to make them as read.
    //

    var box = $(".notifier");

    function markAsRead() {
        $.ajax({
            dataType : "html",
            url : "/ajax/okstfu/",
            success : function(data) { box.html(data); }
        });
        return false;
    }

    function getNotifications() {
        $.ajax({
            dataType : "html",
            url : "/ajax/whatsup/",
            success : function(data) {
                // @todo : Its somewhat stupid to write in the DOM on each refresh.
                box.html(data);
                $(".notifier li.mark a").click(markAsRead);
                setTimeout(getNotifications, 1.5 * 60 * 1000);
            }
        });
    }

    if(box.length > 0) {
        setTimeout(getNotifications, 300);
    }

});
