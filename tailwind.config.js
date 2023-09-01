module.exports = {
  darkMode: 'class',
  content: [
    './app/Http/Controllers/*.php',
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './modules/**/*.blade.php',
    './modules/**/*.vue',
    './modules/**/*.php'
  ],
  theme: {
    extend: {
      colors: {
        'sidebar-light': '#2d353c',
        'sidebar-darker': '#1a2229',
        'lime-nmsprime': '#98d145',
        'whitesmoke' : '#f5f5f5',
        'gainsboro' : '#dcdcdc',
      },
      screens: {
        'wide': '1920px',
      }
    }
  },
  plugins: [
    require('tailwind-scrollbar')({ nocompatible: true }),
  ],
  corePlugins: {
    visibility: false
  },
  safelist: [
    'bg-whitesmoke',
    'bg-gainsboro'
  ],
}
