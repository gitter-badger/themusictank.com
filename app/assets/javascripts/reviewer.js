$(function() {

    var _uri = null,
        _dom = {
            'loading'   : $("<div class=\"loading-wrap\"><i class=\"fa fa-refresh fa-spin fa-fw\"></i></div>"),
            'dialog'    : null,
            'content'   : null,
            'body'      : $("body")
        };

    // Inserts the dialog in the dom with the proper events.
    function _createDialog() {
        // Prepare the box and assign the events
        _dom['dialog'] = $("<div id=\"reviewer\" class=\"modal fade\"><div class=\"modal-dialog\"><div class=\"modal-content\"></div></div></div>")
            .modal({backdrop: 'static', keyboard: false});

        _dom['content'] = _dom['dialog'].find(".modal-content");

        _dom['dialog'].on('hidden.bs.modal', function () {
            _dom['body'].removeClass("dialog-open");
        });
    }

    // Opens the dialog.
    function _openDialog() {
        _dom['content'].html(_dom['loading']);

        _dom['body'].addClass("dialog-open");
        _dom['dialog'].modal("show");
    }

    function _post_onSuccess(response) {
        _dom['content'].html(response);
    }

    $("form.review").submit(function(evt){
        evt.preventDefault();

        // Ensure we have a dialog to work with.
        if (!_dom['dialog']) {
            _createDialog();
        }

        _openDialog();
        $.post($(this).attr('action'), $(this).serialize(), _post_onSuccess);
    });

});
