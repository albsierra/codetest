const colors = require("tailwindcss/colors");

module.exports = {
  mode: 'jit',
  purge: {
    enabled: true,
    content: [
      "./assets/**/*.js",
      "./assets/**/*.scss}",
      './views/**/*.twig',
    ],
  },
  darkMode: "class",
  theme: {
    fontFamily: {
      'sans': ['Roboto'],
     },
    extend: {
      colors: {
        primary: "#05A1E6",
        secondary: "#A274C1",
        greyish: "#696267",
        sky: colors.sky,
        cyan: colors.cyan,
        blueGray: colors.blueGray,
        indigo: colors.indigo,
        violet: colors.violet,
        orange: colors.orange,
        emerald: colors.emerald,
      }
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
};