module.exports = {
  darkMode: 'class',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './modules/**/*.blade.php',
    './modules/**/*.vue'
  ],
  theme: {
    extend: {
      colors: {
        'sidebar-light': '#2d353c',
        'lime-nmsprime': '#98d145',
        'whitesmoke' : '#f5f5f5',
        'gainsboro' : '#dcdcdc',
      } 
    }
  },
  plugins: [],
  safelist: [
    'bg-whitesmoke',
    'bg-gainsboro'
  ]
}
