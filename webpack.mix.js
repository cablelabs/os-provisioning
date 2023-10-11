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

const mix = require('laravel-mix')
const path = require('path')
const fs = require("fs")

/* Allow multiple Laravel Mix applications */
require('laravel-mix-merge-manifest');
mix.mergeManifest();

/* Allow alternative env file */
require('mix-env-file')
mix.env(process.env.ENV_FILE)

/* Make alias for main resource/js directory */
mix.alias({
  '~': path.join(__dirname, 'resources'),
  '@': path.join(__dirname, 'resources/js')
});

mix.js('resources/js/app.js', 'public/js')
  .postCss('resources/css/app.css', 'public/css', [
    require('tailwindcss'),
  ])

/* read out the available modules */
let modules = JSON.parse(fs.readFileSync("modules_statuses.json").toString());

for (let module in modules) {
  if (! modules[module]) {
    continue
  }

  let moduleLower = module.toLowerCase();

  for (let extension of ['js', 'css']) {
    if (! fs.existsSync(`modules/${module}/Resources/${extension}/${moduleLower}.${extension}`)) {
      if (! fs.existsSync(`modules/${module}/Resources/${extension}`)) {
        fs.mkdirSync(`modules/${module}/Resources/${extension}`)
      }

      fs.writeFileSync(`modules/${module}/Resources/${extension}/${moduleLower}.${extension}`, '')
    }

    if (extension === 'js') {
      mix.js(`modules/${module}/Resources/${extension}/${moduleLower}.${extension}`, `public/${extension}`)
    } else {
      mix.postCss(`modules/${module}/Resources/${extension}/${moduleLower}.${extension}`, `public/${extension}`)
    }
  }
}

if (mix.inProduction()) {
  mix.postCss('resources/css/vendor.css', 'public/css', [
    require('postcss-purgecss-laravel')({
      extend: {
        content: [
          path.join(__dirname, 'modules/**/*.php'),
          path.join(__dirname, 'modules/**/*.js'),
          path.join(__dirname, 'modules/**/*.vue'),
          path.join(__dirname, 'public/components/assets-admin/js/apps.js'),
        ],
      },
      safelist: {
        standard: [/tooltip$/],
        greedy: [/select2.*/, /pace.*/, /datatable.*/, /dataTable.*/, /dt.*/, /col-.*/, /snotify.*/]
      },
    })
  ])
} else {
  mix.postCss('resources/css/vendor.css', 'public/css')
}

// extra assets
  mix.js('resources/js/leaflet/pixi-overlay-tools.js', 'public/js/leaflet')
  mix.js('resources/js/assets/nmsprime-canvas.js', 'public/js')

  // copy from node_modules do public
mix.copy('node_modules/jszip/dist/jszip.min.js', 'public/js/jszip.min.js')
  .copy('node_modules/pdfmake/build/pdfmake.min.js', 'public/js/pdfmake.min.js')
  .copy('node_modules/pdfmake/build/vfs_fonts.js', 'public/js/vfs_fonts.js')
  .copy([
    'node_modules/leaflet/dist/leaflet.js',
    'node_modules/leaflet-draw/dist/leaflet.draw.js',
    'node_modules/pixi.js/dist/browser/pixi.js',
    'node_modules/leaflet-pixi-overlay/L.PixiOverlay.js',
    'node_modules/leaflet.gridlayer.googlemutant/dist/Leaflet.GoogleMutant.js',
    'node_modules/leaflet.heat/dist/leaflet-heat.js',
  ], 'public/js/leaflet/')
  .copy([
    'node_modules/leaflet/dist/leaflet.css',
    'node_modules/leaflet-draw/dist/leaflet.draw.css',
  ], 'public/css/leaflet/')
  .copy([
    'node_modules/leaflet-draw/dist/images',
  ], 'public/css/leaflet/images');

/* Enable gzip and brotli compression for bundled files*/
require('laravel-mix-compress')
mix.compress({
  productionOnly: true,
});

mix.webpackConfig(webpack => {
  return {
      plugins: [
          new webpack.ProvidePlugin({
              $: 'jquery',
              jQuery: 'jquery',
              'window.jQuery': 'jquery'
          })
      ]
  }
})

// extract
mix.extract(['pace-js'], '/js/pace.js')
mix.extract();

mix.vue()
mix.version()
