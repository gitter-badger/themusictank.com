(function ($, undefined) {

    "use strict";

    var UpvoteForm = namespace("Tmt.Components").UpvoteForm = function (ajaxForm) {
        this.ajaxForm = ajaxForm;
        this.element = ajaxForm.element;

        this.initialize();
    };

    inherit([Tmt.EventEmitter], UpvoteForm, {

        "initialize": function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            this.addEvents();
            resetButtons.call(this);
            this.element.addClass("initialized");
        },

        "addEvents": function () {
            this.element.find("button").click(onButtonClick.bind(this));
            this.ajaxForm.on('submitSuccess', onSubmitSuccess.bind(this));
        },

        "getType": function () {
            return this.element.data("upvote-type");
        },

        "getObjectId": function () {
            return this.element.data("upvote-object-id");
        },

        "isTrack": function () {
            return this.getType() == "track";
        },

        "isAlbum": function () {
            return this.getType() == "album";
        },

        "setValue": function (value) {
            this.element.removeClass("liked disliked");
            this.element.find("input[name=vote]").val(value);

            if (value == 1) {
                this.element.addClass("liked");
                enableButton(this.element.find('button.up'));
            } else if (value == 2) {
                this.element.addClass("disliked");
                this.element.find('button.down').html('<i class="fa fa-thumbs-down" aria-hidden="true">');
            } else {
                resetButtons.call(this);
            }

            this.emit('valueChange', value, this);
        },

        "getValue": function () {
            return this.element.find("input[name=vote]").val();
        },

        "lock": function () {
            this.element.find("button").attr("disabled", "disabled");
        },

        "unlock": function () {
            this.element.find("button").removeAttr("disabled");
        }
    });

    function resetButtons() {
        this.element.find('button.up').html('<i class="fa fa-thumbs-o-up" aria-hidden="true">');
        this.element.find('button.down').html('<i class="fa fa-thumbs-o-down" aria-hidden="true">');
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
        var clickedValue = evt.target.value;

        if (clickedValue != this.getValue()) {
            this.setValue(clickedValue);
        } else {
            // twice the same value means the user wants to cancel
            this.setValue(-1);
        }

        this.lock();
        this.element.submit();
    }

    function onSubmitSuccess(response, ajaxForm) {
        if (response && response.vote) {
            this.setValue(response.vote);
        }

        this.unlock();
    }

})(jQuery);