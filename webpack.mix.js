const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
 const tailwindcss = require('tailwindcss');

 mix.js("resources/js/app.js", "public/js")
 .vue()
 .sass("resources/css/app.scss","public/css")
 .sass("resources/components/assets-admin/css/style.scss", "public/components/assets-admin/css")
 .options({
	processCssUrls: false,
	postCss: [ tailwindcss('./tailwind.config.js') ],
});
