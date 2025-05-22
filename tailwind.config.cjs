// tailwind.config.cjs
/** @type {import('tailwindcss').Config} */
const { fontFamily, animation, keyframes } = require('tailwindcss/defaultTheme')

module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './resources/**/*.vue',
  ],

  safelist: [
    // your decade theme wrappers
    { pattern: /^decade-\d{4}$/ },
    // slider / tabs
    'tab-btn',
    'active',
    'fade-in',
    // year filter
    'hidden',
    // noUiSlider classes
    { pattern: /^noUi-/ },
    // your theme‐wrapper base ID
    'theme-wrapper',
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
    // your custom base plugin
    function ({ addBase, theme }) {
      addBase({
        html: { fontFamily: theme('fontFamily.orbitron') },
      })
    },
    // any other Tailwind plugins…
  ],
}