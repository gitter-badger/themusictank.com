$(function() {

    // Automate bug reporting
    // ----------------------
    //
    // Each time an element has a bug type on it, listen for clicks and build a report
    // based on the info.
    //
    // Aim to be as unobstrusive as possible because otherwise people won't use it.
    // Also aim not the create a gaping door where people will spam that they hate
    // Creed as a bug, valid as this may be.
    //

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
        _dom['dialog'] = $("<div id=\"bugreport\" class=\"modal fade\"><div class=\"modal-dialog\"><div class=\"modal-content\"></div></div></div>").modal();
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

    function _getReport(data) {
        $.get(_uri, data, _getReport_onSuccess);
    }

    function _postReport(data, noCallback) {
        // Assume the form url is the same as the original GET url.
        $.post(_uri, data, noCallback !== true ? _postReport_onSuccess : null);
    }

    function _getReport_onSuccess(response) {
        _dom['content'].html(response);

        // Once we have a form to work with, hook into it to prevent
        // natural submission.
        _dom['content'].find("form").on("submit", _form_onSubmit);
    }

    function _postReport_onSuccess(response)
    {
        _dom['content'].html(response);

        // Dont print another submit button, hook the form's submit into the window's close button
        // Make sure this event is allowed to have the default dehaviour
        _dom['content'].find(".btn-default").click(_close_onClick);
    }


    // Dont print another submit button, hook the form's submit into the window's close button
    // Make sure this event is allowed to have the default dehaviour
    function _close_onClick(evt) {
        var data = _dom['content'].find("form").serialize();
        _postReport(data, true);
    }

    function _form_onSubmit(evt) {
        evt.preventDefault();
        var data = $(this).serialize();

        // Give cue that something is happening.
        _dom['content'].html(_dom['loading']);

        // Post the data
        _postReport(data);
    }

    $("*[data-bug-iden]").click(function(evt){
        evt.preventDefault();

        // Save the context of the current process
        _uri = $(this).attr("href");

        // Ensure we have a dialog to work with.
        if (!_dom['dialog']) {
            _createDialog();
        }

        _openDialog();
        _getReport({'iden': $(this).attr("data-bug-iden"), 'location': $(this).attr("data-bug-location")});
    });

});
