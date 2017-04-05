(function ($, undefined) {

    "use strict";

    var reviewerWindow;

    var ReviewerInitializer = namespace("Tmt.Initializers").ReviewerInitializer = function () {
        this.initialize();
    };

    inherit([Tmt.EventEmitter], ReviewerInitializer, {
        'build': function (app) {
            reviewerWindow = $('[data-attr="tmt-reviewer"]');
            if (reviewerWindow.length > 0) {
                app.initializers.PlayerInitializer.on('bound', bindToPlayer.bind(this));
            }
        }
    });

    function bindToPlayer(playerInitializer) {
        if (playerInitializer.players.length === 1) {
            playerInitializer.players[0].on('embeded', function(player) {
                player.getStreamer().addEventListener('onReady', function() {
                    new Tmt.Components.Reviewer.Reviewer(reviewerWindow, player);
                });
            });
        }
    }

})(jQuery);
