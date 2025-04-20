import { defineConfig } from 'tailwindcss'

export default defineConfig({
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  
  safelist: [
    'animate-float',
    'animate-wave',
    'animate-floatWave',
  ],

  theme: {
    extend: {
      fontFamily: {
        orbitron: ['Orbitron', 'sans-serif'],
      },
      keyframes: {
        wave: {
          '0%': { transform: 'rotate(0deg)' },
          '15%': { transform: 'rotate(10deg)' },
          '30%': { transform: 'rotate(-6deg)' },
          '45%': { transform: 'rotate(8deg)' },
          '60%': { transform: 'rotate(-4deg)' },
          '75%': { transform: 'rotate(6deg)' },
          '100%': { transform: 'rotate(0deg)' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-6px)' },
        },
        floatWave: {
          '0%':   { transform: 'translateY(0) rotate(0deg)' },
          '15%':  { transform: 'translateY(-4px) rotate(15deg)' },
          '30%':  { transform: 'translateY(-8px) rotate(-10deg)' },
          '45%':  { transform: 'translateY(-10px) rotate(12deg)' },
          '60%':  { transform: 'translateY(-6px) rotate(-6deg)' },
          '75%':  { transform: 'translateY(-3px) rotate(8deg)' },
          '100%': { transform: 'translateY(0) rotate(0deg)' },
        },        
      },
      animation: {
        float: 'float 2s ease-in-out infinite',
        wave: 'wave 2.5s ease-in-out infinite',
        floatWave: 'floatWave 3s ease-in-out infinite',
      },
    },
  },
  plugins: [
    function ({ addBase, theme }) {
      addBase({
        html: { fontFamily: theme('fontFamily.orbitron') }
      })
    }
  ]
})
