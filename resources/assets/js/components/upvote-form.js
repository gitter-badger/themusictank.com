(function ($, undefined) {

    "use strict";

    var ajaxForm,
        rootNode,
        enabled = false;

    var UpvoteForm = namespace("Tmt.Components").UpvoteForm = function (ajaxFormObj) {
        ajaxForm = ajaxFormObj;
        rootNode = ajaxForm.element;

        this.initialize();
    };

    inherit([Tmt.EventEmitter], UpvoteForm, {

        "initialize": function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            addEvents.bind(this);
            resetButtons();
        },

        "getType": function () {
            return rootNode.data("upvote-type");
        },

        "getObjectId": function () {
            return rootNode.data("upvote-object-id");
        },

        "setObjectId": function (id) {
            return rootNode.data("upvote-object-id", id);
        },

        "isTrack": function () {
            return this.getType() == "track";
        },

        "isAlbum": function () {
            return this.getType() == "album";
        },

        "setValue": function (value) {
            rootNode.removeClass("liked disliked");
            rootNode.find("input[name=vote]").val(value);

            if (value == 1) {
                rootNode.addClass("liked");
                enableButton(rootNode.find('button.up'));
            } else if (value == 2) {
                rootNode.addClass("disliked");
                rootNode.find('button.down').html('<i class="fa fa-thumbs-down" aria-hidden="true">');
            } else {
                resetButtons();
            }

            this.emit('valueChange', value, this);
        },

        "getValue": function () {
            return rootNode.find("input[name=vote]").val();
        },

        "lock": function () {
            enabled = false;
            rootNode.find("button").attr("disabled", "disabled");
        },

        "unlock": function () {
            enabled = true;
            rootNode.find("button").removeAttr("disabled");
        }
    });

    function addEvents() {
        rootNode.find("button").click(onButtonClick.bind(this));
        ajaxForm.on('submitSuccess', onSubmitSuccess.bind(this));
    };

    function resetButtons() {
        rootNode.find('button.up').html('<i class="fa fa-thumbs-o-up" aria-hidden="true">');
        rootNode.find('button.down').html('<i class="fa fa-thumbs-o-down" aria-hidden="true">');
    }

    function enableButton(button) {
        if (button.hasClass("up")) {
            button.html('<i class="fa fa-thumbs-up" aria-hidden="true">');
        }
        if (button.hasClass("down")) {
            button.html('<i class="fa fa-thumbs-down" aria-hidden="true">');
        }
    }

    function onButtonClick(evt) {
        if (!enabled) {
            return;
        }

        var $el = $(evt.target),
            button = $el.parents('button'),
            clickedValue = button.val();

        if (clickedValue != this.getValue()) {
            this.setValue(clickedValue);
        } else {
            // twice the same value means the user wants to cancel
            this.setValue(-1);
        }

        this.lock();
        rootNode.submit();
    }

    function onSubmitSuccess(response, ajaxForm) {
        if (response && response.vote) {
            this.setObjectId(response.id);
            this.setValue(response.vote);
        }

        this.unlock();
    }

})(jQuery);
