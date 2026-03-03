import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./vendor/filament/**/*.blade.php",
  ],
  theme: {
    extend: {
      colors: {
        rg: {
          deepPurple: "#2D0A3E",
          darkPlum: "#6B2D5E",
          purple: "#8E44AD",
          midPurple: "#9B59B6",
          lavender: "#C39BD3",
          lightLavender: "#E8D5F0",
          rosePink: "#E8B4D0",
          mauve: "#C4789B",
          leafGreen: "#4A7C59",
          cream: "#FAF7F5",
          warmWhite: "#FDF8F5",
          darkText: "#2D1B33",
          grayText: "#7B6882",
        },
      },
      fontFamily: {
        display: ["Playfair Display", "Georgia", "serif"],
        script: ["Great Vibes", "cursive"],
        sans: ["Nunito", "system-ui", "sans-serif"],
      },
      borderRadius: {
        card: "12px",
        btn: "8px",
      },
    },
  },
  plugins: [typography],
};

