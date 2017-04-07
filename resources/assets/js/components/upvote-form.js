(function ($, undefined) {

    "use strict";


    var UpvoteForm = namespace("Tmt.Components").UpvoteForm = function (ajaxFormObj) {
        this.ajaxForm = ajaxFormObj;
        this.rootNode = ajaxFormObj.getRootNode();
        this.enabled = false;

        this.initialize();
    };

    inherit([Tmt.EventEmitter], UpvoteForm, {

        "initialize": function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            addEvents.call(this);
            resetButtons.call(this);

            this.rootNode.addClass('initialized');
        },

        "getType": function () {
            return this.rootNode.data("upvote-type");
        },

        "getObjectId": function () {
            return this.rootNode.data("upvote-object-id");
        },

        "setObjectId": function (id) {
            return this.rootNode.data("upvote-object-id", id);
        },

        "isTrack": function () {
            return this.getType() == "track";
        },

        "isAlbum": function () {
            return this.getType() == "album";
        },

        "setValue": function (value) {
            this.rootNode.removeClass("liked disliked");
            this.rootNode.find("input[name=vote]").val(value);

            if (value == 1) {
                this.rootNode.addClass("liked");
                enableButton(this.rootNode.find('button.up'));
            } else if (value == 2) {
                this.rootNode.addClass("disliked");
                this.rootNode.find('button.down').html('<i class="fa fa-thumbs-down" aria-hidden="true">');
            } else {
                resetButtons.call(this);
            }

            this.emit('valueChange', value, this);
        },

        "getValue": function () {
            return this.rootNode.find("input[name=vote]").val();
        },

        "lock": function () {
            this.enabled = false;
            this.rootNode.find("button").attr("disabled", "disabled");
        },

        "unlock": function () {
            this.enabled = true;
            this.rootNode.find("button").removeAttr("disabled");
        }
    });

    function addEvents() {
        this.rootNode.find("button").click(onButtonClick.bind(this));
        this.ajaxForm.on('submitSuccess', onSubmitSuccess.bind(this));
    };

    function resetButtons() {
        this.rootNode.find('button.up').html('<i class="fa fa-thumbs-o-up" aria-hidden="true">');
        this.rootNode.find('button.down').html('<i class="fa fa-thumbs-o-down" aria-hidden="true">');
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
        if (!this.enabled) {
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
        this.rootNode.submit();
    }

    function onSubmitSuccess(response, ajaxForm) {
        if (response && response.vote) {
            this.setObjectId(response.id);
            this.setValue(response.vote);
        }

        this.unlock();
    }

})(jQuery);
