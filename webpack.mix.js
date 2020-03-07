const mix = require('laravel-mix');


 mix.options({
    hmrOptions: {
        host: 'localhost',
        port: '6663'
    }
})

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');
