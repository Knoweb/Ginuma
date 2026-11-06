/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{js,ts,jsx,tsx}"],
  theme: {
    extend: {
      colors: {
        ginum: {
          navy: "#1A335A",
          "navy-dark": "#122440",
          "navy-light": "#24467d",
          blue: "#90C8EC",
          "blue-soft": "#b8def6",
          "blue-tint": "#eaf4fb",
          black: "#000000",
          white: "#FFFFFF",
        },
      },
      fontFamily: {
        heading: ["Arvo", "Georgia", "serif"],
        serif: ["Arvo", "Georgia", "serif"],
        sans: [
          "Helvetica Neue",
          "Helvetica",
          "Arial",
          "-apple-system",
          "BlinkMacSystemFont",
          '"Segoe UI"',
          "Roboto",
          "sans-serif",
        ],
        body: [
          "Helvetica Neue",
          "Helvetica",
          "Arial",
          "-apple-system",
          "BlinkMacSystemFont",
          '"Segoe UI"',
          "Roboto",
          "sans-serif",
        ],
      },
    },
  },
  plugins: [],
};
