// tailwind.config.cjs
/** @type {import('tailwindcss').Config} */
const { fontFamily } = require('tailwindcss/defaultTheme');

const palettes =
  '(slate|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)';
const shades = '(50|100|300|600|700|900|950)';

module.exports = {
  darkMode: 'class',

  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './resources/**/*.vue',
  ],

  safelist: [
    'hidden',
    'animate-fade-in',

    // Base utilities (bg|text|border)
    { pattern: new RegExp(`^(bg|text|border)-${palettes}-${shades}$`) },

    // Hover variants
    {
      pattern: new RegExp(`^(bg|text|border)-${palettes}-${shades}$`),
      variants: ['hover'],
    },

    // Dark variants
    {
      pattern: new RegExp(`^(bg|text|border)-${palettes}-${shades}$`),
      variants: ['dark'],
    },

    // Fractional dark backgrounds used by buttons (e.g. dark:bg-emerald-950/30, dark:hover:bg-emerald-900/40)
    new RegExp(`^dark:bg-${palettes}-(900|950)\\/(30|40)$`),
    new RegExp(`^dark:hover:bg-${palettes}-(900|950)\\/(30|40)$`),
  ],

  theme: {
    extend: {
      fontFamily: {
        orbitron: ['Orbitron', ...fontFamily.sans],
      },
      animation: {
        'fade-in': 'fadeIn 0.3s ease-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: 0 },
          '100%': { opacity: 1 },
        },
      },
    },
  },

  plugins: [
    function ({ addBase, theme }) {
      addBase({
        html: { fontFamily: theme('fontFamily.orbitron') },
      });
    },
  ],
};