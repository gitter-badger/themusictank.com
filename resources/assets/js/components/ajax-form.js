(function() {

    "use strict";

    /**
     * A form object that can be captured using ajax.
     * @param {jQuery} el
     */
    var AjaxForm = namespace("Tmt.Components").AjaxForm = function(el) {
        this.element = el;
        this.events = [
            'beforeSubmit',
            'bound',
            'render',
            'submit',
            'submitSuccess'
        ];
    };

    inherit([ Evemit ], AjaxForm, {
        init : function() {
            this.addEvents();
        },

        addEvents : function() {
            this.element.on("submit", onSubmit.bind(this));
            this.element.on("onBeforeSubmit", onBeforeSubmit.bind(this));
            this.emit("bound", this);
        }
    });


    function onSubmit(event) {
        event.preventDefault();

        this.emit("beforeSubmit", this, event);

        var formElement = this.element;

        $.ajax({
            url: formElement.attr("action"),
            data: new FormData(formElement.get(0)),
            cache: false,
            processData: false,
            contentType: false,
            type: formElement.attr('method'),
            success: onSubmitSuccess.bind(this)
        });

        this.emit("submit", this);
    };

    function onBeforeSubmit()
    {
        this.element.addClass("working");
    }

    function onSubmitSuccess(response) {
        /*
        var newVersion = $(html);

        this.element.replaceWith(newVersion);
        this.element = newVersion;
        this.addEvents();*/

        this.emit("submitSuccess", response, this);
        // this.emit("afterRender", this);
    }

}());
