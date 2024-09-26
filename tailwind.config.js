/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          blue: '#1A8FE3',
          dark: '#0F1114',
          white: '#FFFFFF',
          light_white: '#F2F2F2',
        },
        secondary: {
          orange: '#F37933',
          orange_hover: '#FB8C00',
          green: '#28A745',
          red: '#FF4C4C',
        },
      },
      maxWidth: {
        '8xl': '1250px',
      },
    },
  },
  plugins: [],
}
