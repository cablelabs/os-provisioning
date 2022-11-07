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
        'dark-black-light' : '#444648',
        'dark-black-lighter' : '#38444d',
        'dark-gray-light' : '#f1f1f1',
        'dark-gray-lighter' : '#ccc',
        'gray-dark' : '#707478',
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
