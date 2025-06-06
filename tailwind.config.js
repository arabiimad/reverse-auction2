// tailwind.config.js
const colors = require('tailwindcss/colors')

module.exports = {
  content: [
    './templates/**/*.twig',
    './assets/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        ...colors
      },
    },
  },
  plugins: [],
}
