var elixir = require('laravel-elixir');

process.env.DISABLE_NOTIFIER = true;

elixir(function(mix) {
    mix.sass('app.scss', 'public/assets/css');

    mix.scripts(
        [
            'globals.js',
            'event-emitter.js',
            'app.js',

            'models/profile.js',

            'components/ajax-form.js',
            'components/player.js',
            'components/upvote-form.js',

            'initializers/ajax-forms-initializer.js',
            'initializers/player-initializer.js',
            'initializers/upvote-forms-initializer.js'
        ],
        'public/assets/js/app.js',
        'resources/assets/js/'
    );

    mix.scripts(
        [
            'jquery/dist/jquery.min.js',
            'evemit/evemit.min.js'
        ],
        'public/assets/js/vendor.js',
        'node_modules/'
    );

    mix.version(['assets/css/app.css', 'assets/js/app.js', 'assets/js/vendor.js']);
});
