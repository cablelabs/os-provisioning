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
        'black-dark': '#1a2229',
        'sidebar-light': '#2d353c',
        'lime-nmsprime': '#98d145',
        'primary-dark' : '#444648',
        'secondary-dark' : '#38444d',
        'primary-gray' : '#f1f1f1',
        'secondary-gray' : '#ccc',
        'gray-dark' : '#707478'
      } 
    }
  },
  plugins: []
}
