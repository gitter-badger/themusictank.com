(function ($, undefined) {

    "use strict";

    var forms = [];

    /**
     * Ajax-enabled forms public bootstraper
     */
    var AjaxFormsInitializer = namespace("Tmt.Initializers").AjaxFormsInitializer = function () {
        this.initialize();
    };

    inherit([Tmt.EventEmitter], AjaxFormsInitializer, {
        'build': function (app) {
            addEvents.call(this, app);
        },

        'getForms': function() {
            return forms;
        }
    });

    function bindPageForms() {
        forms = [];
        $("form[data-ctrl-mode=ajax]").each(function () {
            forms.push(new Tmt.Components.AjaxForm($(this)));
        });

        this.emit('bound', this, forms);
    }

    function addEvents(app) {
        app.on('ready', bindPageForms.bind(this));
    }

})(jQuery);
