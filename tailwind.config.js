import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: "class",
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./vendor/filament/**/*.blade.php",
  ],
  theme: {
    extend: {
      /**
       * Opaklık anahtarları — Tailwind varsayılanında yalnızca 5,10,15…100 (belirli adımlar) vardır.
       * `border-black/8`, `text-white/88`, `bg-white/72` gibi sınıflar anahtar yoksa tamamen yok sayılır (Figma’da
       * tanımlı yumuşak kenarlık / cam yüzeylerin kaybına yol açar). Rose Garden “RG” token’ları burada toplanır.
       */
      opacity: {
        4: "0.04",
        6: "0.06",
        7: "0.07",
        8: "0.08",
        9: "0.09",
        11: "0.11",
        12: "0.12",
        14: "0.14",
        16: "0.16",
        18: "0.18",
        21: "0.21",
        22: "0.22",
        28: "0.28",
        32: "0.32",
        46: "0.46",
        48: "0.48",
        42: "0.42",
        52: "0.52",
        58: "0.58",
        62: "0.62",
        66: "0.66",
        72: "0.72",
        73: "0.73",
        76: "0.76",
        78: "0.78",
        82: "0.82",
        84: "0.84",
        86: "0.86",
        88: "0.88",
        92: "0.92",
        96: "0.96",
      },
      colors: {
        rg: {
          footer: "#17101f",
          deepPurple: "#2a1f35",
          darkPlum: "#513a62",
          purple: "#654878",
          midPurple: "#8a739e",
          lavender: "#b8a3c9",
          lightLavender: "#ebe4f2",
          rosePink: "#E8B4D0",
          mauve: "#C4789B",
          leafGreen: "#4A7C59",
          cream: "#faf8f6",
          warmWhite: "#fdfaf8",
          darkText: "#374151",
          grayText: "#6b7280",
        },
      },
      fontFamily: {
        display: ["Playfair Display", "Georgia", "serif"],
        script: ["Great Vibes", "cursive"],
        sans: ["Inter", "system-ui", "sans-serif"],
      },
      boxShadow: {
        "card-soft":
          "0 2px 16px rgba(55, 65, 81, 0.06), 0 1px 3px rgba(55, 65, 81, 0.04)",
        "card-soft-hover":
          "0 12px 28px rgba(55, 65, 81, 0.1), 0 4px 8px rgba(55, 65, 81, 0.05)",
        /** Üst şerit / yüzen paneller — tek ana gölge (Figma “elevation” benzeri) */
        "rg-float":
          "0 18px 50px -12px rgba(42, 31, 53, 0.12), 0 4px 14px -4px rgba(42, 31, 53, 0.06)",
        "rg-float-dark":
          "0 24px 56px -16px rgba(0, 0, 0, 0.45), 0 8px 20px -8px rgba(0, 0, 0, 0.25)",
      },
      transitionTimingFunction: {
        "rg-out": "cubic-bezier(0.22, 1, 0.36, 1)",
      },
      borderRadius: {
        card: "12px",
        btn: "8px",
      },
    },
  },
  plugins: [typography],
};

