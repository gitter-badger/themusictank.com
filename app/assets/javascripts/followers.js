$(function() {

    // Follow / unfollow subscription buttons
    function _follow_onClick(event)
    {
        var $el = $(this);
        event.preventDefault();
        $.ajax($el.attr("href")).done(function(data)
        {
            var parent = $el.parent();
            parent.html(data);
            parent.find("a.follow, a.unfollow").on("click", _follow_onClick);
        });
    }
    $("a.follow, a.unfollow").on("click", _follow_onClick);

});
