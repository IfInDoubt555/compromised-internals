// tailwind.config.cjs
/** @type {import('tailwindcss').Config} */
const { fontFamily } = require('tailwindcss/defaultTheme')

module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './resources/**/*.vue',
  ],

  // Only safelist real Tailwind utilities you build dynamically
  safelist: [
    'hidden',
    'animate-fade-in',
    // add other real utilities if you truly generate them dynamically
    // e.g. 'md:grid', 'lg:block', 'bg-red-500'
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
