var elixir = require('laravel-elixir');
var jsdoc = require('gulp-jsdoc3');
var glob = require('glob');
var typescript = require('laravel-elixir-typescript');

process.env.DISABLE_NOTIFIER = true;

gulp.task('doc', function (cb) {
    gulp.src(['README.md', './resources/assets/js/**/*.js'], { read: false })
        .pipe(jsdoc(cb));
});

elixir(function (mix) {
    mix.sass('app.scss', 'public/assets/css');

    // mix.scripts(
    //     glob.sync('resources/assets/js/**/*.js'),
    //     'public/assets/js/app.js'
    // );

    mix.scripts(
        [
            'jquery/dist/jquery.min.js',
            'evemit/evemit.min.js',
            'typeahead.js/dist/typeahead.bundle.min.js',
            // for when we can use sound notifications correctly:
            // 'howler/dist/howler.min.js',
            'gsap/src/minified/TweenMax.min.js',
            'gsap/src/minified/utils/Draggable.min.js'
        ],
        'public/assets/js/vendor.js',
        'node_modules/'
    );

    mix.version(['public/assets/css/app.css' /*, 'public/assets/js/app.js'*/, 'public/assets/js/vendor.js']);
});
