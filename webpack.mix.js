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
    'bootbox',
    'moment',
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
            'resources/js/specific/*.js'
        ],
        'public/js/specific.js')
    .scripts([
            'resources/js/plugins/*.js'
        ],
        'public/js/plugins.js')
    .scripts([
            'resources/js/torrentClient.js'
        ],
        'public/js/torrentClient.js')
    .sass('resources/sass/app.scss', 'public/css')
    // .less('resources/less/*.less', 'resources/css/*.less')
    // .styles('resources/css/generic/*.css', 'public/css/generic.css')
    .less('resources/less/generic/cmn-variables.less', 'public/css/generic')
    .less('resources/less/generic/cmn-fonts.less', 'public/css/generic')
    .less('resources/less/generic/cmn-animations.less', 'public/css/generic')
    .less('resources/less/generic/cmn-loaders.less', 'public/css/generic')
    .less('resources/less/generic/cmn-inputs.less', 'public/css/generic')
    .less('resources/less/generic/cmn-styles.less', 'public/css/generic')
    .less('resources/less/common/vue-transitions.less', 'public/css/common')
    .less('resources/less/common/base.less', 'public/css/common')
    .less('resources/less/components/card-episode.less', 'public/css/components')
    .less('resources/less/plugins/Stream.less', 'public/css/plugins')
    .styles([
        'public/css/generic/cmn-variables.css',
        'public/css/generic/cmn-fonts.css',
        'public/css/generic/cmn-animations.css',
        'public/css/generic/cmn-loaders.css',
        'public/css/generic/cmn-inputs.css',
        'public/css/generic/cmn-styles.css',
    ], 'public/css/generic.css')
    .styles([
        'public/css/common/vue-transitions',
        'public/css/common/base.css'
    ], 'public/css/common.css')
    .styles([
        'public/css/components/card-episode.css'
    ], 'public/css/components.css')
    .styles([
        'public/css/plugins/Stream.css'
    ], 'public/css/plugins.css')
    .styles(['resources/less/plugins/Stream.css'], 'public/css/plugins.css')
    .browserSync('serie.local')
    .disableNotifications();
