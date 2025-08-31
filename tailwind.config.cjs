// tailwind.config.cjs
/** @type {import('tailwindcss').Config} */
const { fontFamily } = require('tailwindcss/defaultTheme')

const COLORS = [
  'slate','stone','red','orange','amber','yellow','lime','green',
  'emerald','teal','cyan','sky','blue','indigo','violet','purple',
  'fuchsia','pink','rose',
]

// expand a set of templates for each color token
const BTN_TEMPLATES = [
  'border-{c}-400',
  'text-{c}-700',
  'bg-{c}-100',
  'hover:bg-{c}-200',
  'ring-{c}-500/20',
  'dark:border-{c}-600',
  'dark:text-{c}-300',
  'dark:bg-{c}-950/40',
  'dark:hover:bg-{c}-900/50',
  'dark:ring-{c}-400/20',
]

const btnSafelist = COLORS.flatMap(c =>
  BTN_TEMPLATES.map(t => t.replace('{c}', c))
)

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
    // prose and ring base used in your article card
    'prose','dark:prose-invert','ring-1',
    // keep any other dynamic util you use elsewhere
    // …
    // buttons generated from Board::accentButtonClasses()
    ...btnSafelist,
  ],
  theme: {
    extend: {
      fontFamily: {
        orbitron: ['Orbitron', ...fontFamily.sans],
      },
      animation: { 'fade-in': 'fadeIn 0.3s ease-out' },
      keyframes: {
        fadeIn: { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
      },
    },
  },
  plugins: [
    function ({ addBase, theme }) {
      addBase({ html: { fontFamily: theme('fontFamily.orbitron') } })
    },
  ],
}