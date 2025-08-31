// tailwind.config.cjs
/** @type {import('tailwindcss').Config} */
const { fontFamily } = require('tailwindcss/defaultTheme')

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
    {
      pattern: /(bg|text|border)-(slate|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|  purple|fuchsia|pink|rose)-(50|100|300|600|700|900|950)/,
      variants: ['hover','dark'],
  },
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
      })
    },
  ],
}