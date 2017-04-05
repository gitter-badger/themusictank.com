(function () {

    "use strict";

    /**
     * A form object that can be captured using ajax.
     * @param {jQuery} el
     */
    var AjaxForm = namespace("Tmt.Components").AjaxForm = function (el) {
        this.rootNode = el;
        this.initialize();
    };

    inherit([Tmt.EventEmitter], AjaxForm, {
        initialize: function () {
            Tmt.EventEmitter.prototype.initialize.call(this);
            addEvents.call(this);
        },

        getRootNode: function () {
            return this.rootNode;
        }
    });


    function addEvents() {
        this.rootNode.on("submit", onSubmit.bind(this));
        this.emit("bound", this);
    }

    function onSubmit(event) {
        event.preventDefault();

        this.rootNode.addClass("working");
        this.emit("beforeSubmit", this, event);

        $.ajax({
            url: this.rootNode.attr("action"),
            data: new FormData(this.rootNode.get(0)),
            cache: false,
            processData: false,
            contentType: false,
            type: this.rootNode.attr('method'),
            success: onSubmitSuccess.bind(this)
        });

        this.emit("submit", this);
    }

    function onSubmitSuccess(response) {
        this.rootNode.removeClass("working");
        this.emit("submitSuccess", response, this);
    }

}());
