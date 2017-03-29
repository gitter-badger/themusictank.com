(function($, undefined) {

    "use strict";

    /**
     * Ajax-enabled forms public bootstraper
     */
    var AjaxFormsInitializer = namespace("Tmt.Initializers").AjaxFormsInitializer = function() {
        this.forms = [];
        this.events = [
            "bound"
        ];
    };

    inherit([ Evemit ], AjaxFormsInitializer, {
        'build' : function(app) {
            addEvents.call(this, app);
        }
    });

    function bindPageForms() {
        var forms = [];

        $("form[data-ctrl-mode=ajax]").each(function(){
            var form = new Tmt.Components.AjaxForm($(this));
            form.init();
            forms.push(form);
        });

        this.forms = forms;
        this.emit('bound', this);
    }

    function addEvents(app) {
        app.on('ready', bindPageForms.bind(this));
    }

}(jQuery));
