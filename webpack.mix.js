const mix = require('laravel-mix');
const path = require('path')

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

/* Allow multiple Laravel Mix applications */
require('laravel-mix-merge-manifest');
mix.mergeManifest();

/* Make alias for main resource/js directory */
mix.alias({
  '@': path.join(__dirname, 'resources/js')
});

mix.js('resources/js/app.js', 'public/js')
  .js('modules/CoreMon/Resources/assets/js/core-mon.js', 'public/js')
  .js('modules/HfcBase/Resources/assets/js/hfc-base.js', 'public/js')
  .js('modules/ProvBase/Resources/assets/js/prov-base.js', 'public/js')
  .js('modules/ProvMon/Resources/assets/js/prov-mon.js', 'public/js')
  .js('modules/Ticketsystem/Resources/js/ticketsystem.js', 'public/js')
  .js('modules/Ccc/Resources/assets/js/ccc.js', 'public/js')
  .vue()
  .version()
  .postCss('resources/css/app.css', 'public/css', [
    require('tailwindcss'),
  ])
  .postCss('modules/Ccc/Resources/assets/css/ccc.css', 'css')
  .copy('node_modules/jstree/dist/themes/default/style.min.css', 'public/css/jstree/style.min.css')
  .copy('node_modules/jstree/dist/themes/default/throbber.gif', 'public/css/jstree/throbber.gif')
  .copy('node_modules/jstree/dist/themes/default/32px.png', 'public/css/jstree/32px.png')
  .copy('node_modules/jstree/dist/themes/default/40px.png', 'public/css/jstree/40px.png')
  .extract()
