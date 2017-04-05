(function () {

    "use strict";

    var rootNode;

    /**
     * A form object that can be captured using ajax.
     * @param {jQuery} el
     */
    var AjaxForm = namespace("Tmt.Components").AjaxForm = function (el) {
        rootNode = el;
        this.initialize();
    };

    inherit([Tmt.EventEmitter], AjaxForm, {
        initialize: function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            addEvents.bind(this);
        }
    });


    function addEvents() {
        rootNode.on("submit", onSubmit.bind(this));
        rootNode.on("onBeforeSubmit", onBeforeSubmit.bind(this));
        this.emit("bound", this);
    }

    function onSubmit(event) {
        event.preventDefault();

        this.emit("beforeSubmit", this, event);

        $.ajax({
            url: rootNode.attr("action"),
            data: new FormData(rootNode.get(0)),
            cache: false,
            processData: false,
            contentType: false,
            type: rootNode.attr('method'),
            success: onSubmitSuccess.bind(this)
        });

        this.emit("submit", this);
    };

    function onBeforeSubmit() {
        rootNode.addClass("working");
    }

    function onSubmitSuccess(response) {
        this.emit("submitSuccess", response, this);
    }

}());
