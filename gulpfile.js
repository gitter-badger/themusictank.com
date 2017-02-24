var elixir = require('laravel-elixir');

elixir(function(mix) {
    mix.sass('app.scss', 'public/assets/css');

    mix.scripts(['app.js', 'player.js'], 'public/assets/js/app.js', 'resources/assets/js/');
    mix.scripts(['jquery/dist/jquery.min.js'], 'public/assets/js/vendor.js', 'node_modules/');

    // mix.scripts(
    //     [
    //         // 'jquery.js',
    //         // 'plugins/prism.js',
    //         // 'plugins/bootstrap.js',
    //         // 'plugins/scotchPanels.js',
    //         // 'plugins/algoliasearch.js',
    //         // 'plugins/typeahead.js',
    //         // 'plugins/hogan.js',
    //         // 'plugins/mousetrap.js',

    //     ],
    //     'public/assets/js/app.js',
    //     'resources/assets/js/'
    // );

    mix.version(['assets/css/app.css', 'assets/js/app.js', 'assets/js/vendor.js']);
});
