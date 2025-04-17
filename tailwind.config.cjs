import { defineConfig } from 'tailwindcss'

export default defineConfig({
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    // etc...
  ],
  theme: {
    extend: {
      fontFamily: {
        orbitron: ['Orbitron','sans‑serif'],
        // inter: ['Inter','sans‑serif'], if you still need it
      }
    }
  },
  plugins: [
    // optional: make Orbitron the default for <html>
    function ({ addBase, theme }) {
      addBase({
        html: { fontFamily: theme('fontFamily.orbitron') }
      })
    }
  ]
})
