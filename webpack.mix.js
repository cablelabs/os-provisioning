const mix = require('laravel-mix');
const path = require('path')
require('laravel-mix-compress');

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
  '~': path.join(__dirname, 'resources'),
  '@': path.join(__dirname, 'resources/js'),
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
  .postCss('modules/Ccc/Resources/assets/css/ccc.css', 'css');

  // copy from node_modules do public
  mix.copy('node_modules/jszip/dist/jszip.min.js', 'public/js/jszip.min.js')
  .copy('node_modules/pdfmake/build/pdfmake.min.js', 'public/js/pdfmake.min.js')
  .copy('node_modules/pdfmake/build/vfs_fonts.js', 'public/js/vfs_fonts.js')
  .copy([
    'node_modules/leaflet/dist/leaflet.js',
    'node_modules/leaflet-draw/dist/leaflet.draw.js',
    'node_modules/pixi.js/dist/pixi.js',
    'node_modules/leaflet-pixi-overlay/L.PixiOverlay.js',
    'node_modules/leaflet.gridlayer.googlemutant/dist/Leaflet.GoogleMutant.js',
  ], 'public/js/leaflet/')
  .copy([
    'node_modules/leaflet/dist/leaflet.css',
    'node_modules/leaflet-draw/dist/leaflet.draw.css',
  ], 'public/css/leaflet/')
  .copy([
    'node_modules/leaflet-draw/dist/images',
  ], 'public/css/leaflet/images');

  // compress
  mix.compress({
    productionOnly: true,
  });

  // extract
  mix.extract(['pace-js'], '/js/pace.js');
  mix.extract();
