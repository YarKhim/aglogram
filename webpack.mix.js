const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .version(); // Добавьте версионирование для кэширования
// const mix = require('laravel-mix');

mix.js('resources/js/forge.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');
