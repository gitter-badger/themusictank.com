var elixir = require('laravel-elixir');
var jsdoc = require('gulp-jsdoc3');

process.env.DISABLE_NOTIFIER = true;

gulp.task('doc', function (cb) {
    gulp.src(['README.md', './resources/assets/js/**/*.js'], {read: false})
        .pipe(jsdoc(cb));
});

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
            'components/search-form.js',
            'components/notifier.js',

            'components/reviewer/knob.js',
            'components/reviewer/reviewer.js',

            'initializers/ajax-forms-initializer.js',
            'initializers/player-initializer.js',
            'initializers/reviewer-initializer.js',
            'initializers/profile-initializer.js',
            'initializers/search-initializer.js',
            'initializers/upvote-forms-initializer.js'
        ],
        'public/assets/js/app.js',
        'resources/assets/js/'
    );


//            'howler/dist/howler.min.js'
    mix.scripts(
        [
            'jquery/dist/jquery.min.js',
            'evemit/evemit.min.js',
            'typeahead.js/dist/typeahead.bundle.min.js',
            'gsap/src/minified/TweenMax.min.js',
            'gsap/src/minified/utils/Draggable.min.js'
        ],
        'public/assets/js/vendor.js',
        'node_modules/'
    );

    mix.version(['assets/css/app.css', 'assets/js/app.js', 'assets/js/vendor.js']);
});
