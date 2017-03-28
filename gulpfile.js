var elixir = require('laravel-elixir');

elixir(function(mix) {
    mix.sass('app.scss', 'public/assets/css');

    mix.scripts(
        [
            'globals.js',
            'app.js',
            'player.js',
            'components/ajax-forms.js',
            'components/upvotes.js'
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
