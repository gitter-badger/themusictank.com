(function($, undefined) {

    "use strict";

    /**
     * Ajax-enabled forms public bootstraper
     */
    var AjaxFormsInitializer = namespace("Tmt.Initializers").AjaxFormsInitializer = function(app) {
        this.forms = [];
    };

    AjaxFormsInitializer.prototype = extend([ Evemit ], {
        'build' : function(app) {
            addEvents(app).bind(this);
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
