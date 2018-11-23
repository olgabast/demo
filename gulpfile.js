const elixir = require('laravel-elixir');

require('laravel-elixir-vue');





elixir(mix => {
    mix
        .copy('node_modules/admin-lte/bootstrap/fonts/', 'public/fonts/bootstrap')
        .copy('node_modules/font-awesome/fonts', 'public/fonts')

        .sass('app.scss', 'resources/assets/css/sass.css')
        .less('app.less', 'resources/assets/css/less.css')
        .styles([
            'sass.css',
            'less.css',
            'vendor/*.css',
        ])
        .webpack('app.js', './resources/assets/js/dist')
        .scripts([
            'dist/app.js',
            'vendor/moment.js',
            'vendor/bootstrap-datetimepicker.min.js'
        ]);
        //.version(['public/css/all.css', 'public/js/app.js']);
});
