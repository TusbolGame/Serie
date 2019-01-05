const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .extract([
    'jquery',
    'bootstrap',
    'axios',
    'lodash',
    'popper.js',
    'vue'
])
    .scripts([
            'resources/js/generic/*.js',
        ],
        'public/js/generic.js')
    .scripts([
            'resources/js/common/*.js',
        ],
        'public/js/common.js')
    .scripts([
            'resources/js/plugins/*.js'
        ],
        'public/js/plugins.js')
    .sass('resources/sass/app.scss', 'public/css')
    // .less('resources/less/*.less', 'resources/css/*.less')
    // .styles('resources/css/generic/*.css', 'public/css/generic.css')
    .styles([
        'resources/less/common/base.css'
    ], 'public/css/common.css')
    .styles(['resources/less/plugins/Stream.css'], 'public/css/plugins.css')
    .browserSync('wallpapers.local')
    .disableNotifications();
