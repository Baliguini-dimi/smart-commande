/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#F59E0B",
        dark: "#0a0a0f",
      },
    },
  },
  plugins: [],
}