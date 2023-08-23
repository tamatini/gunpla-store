/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
      "./assets/**/*.js",
      "./templates/**/*.html.twig"
  ],
  theme: {
      colors: {
          'gundam-white': '#ffffff',
          'gundam-red': '#fb2f38',
          'gundam-blue': '#2c52b3',
          'gundam-grey': '#5a5b6d',
          'gundam-yellow': '#fff867'
      },
      extend: {},
  },
    plugins: [],
}

