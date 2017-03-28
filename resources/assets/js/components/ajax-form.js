(function() {

    "use strict";


    /**
     * A form object that can be captured using ajax.
     * @param {jQuery} el
     */
    var AjaxForm = namespace("Tmt.Components") = function(el) {
        this.element = el;
        this.listeners = {
            'onBeforeSubmit' : [],
            'onBound' : [],
            'onRender' : [],
            'onSubmit' : []
        };
    };

    AjaxForm.prototype = extend([ Evemit ], {

        addListener : function(key, callback) {
            this.listeners[key].push(callback);
        },

        fireEvent : function (key) {
            if (this.listeners[key]) {
                for( var i = 0, len = this.listeners[key].length; i < len; i++) {
                    this.listeners[key][i]();
                }
            }
        },

        init : function() {
            this.addEvents();
        },

        addEvents : function() {
            this.element.on("submit", onSubmit.bind(this));
            this.addListener("onBeforeSubmit", onBeforeSubmit.bind(this));
            this.fireEvent('onBound', [this]);
        }
    });


    function onSubmit(event) {
        event.preventDefault();

        this.fireEvent("onBeforeSubmit", [this, event]);

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

        this.fireEvent("onSubmit", [this]);
    };

    function onBeforeSubmit()
    {
        this.element.addClass("working");
    }

    function onSubmitSuccess(html) {
        var newVersion = $(html);

        this.element.replaceWith(newVersion);
        this.element = newVersion;
        this.addEvents();

        this.fireEvent("afterRender", [this]);
    }

}());
