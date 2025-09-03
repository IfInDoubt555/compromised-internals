// tailwind.config.cjs
/** @type {import('tailwindcss').Config} */
const { fontFamily } = require('tailwindcss/defaultTheme')
const typography = require('@tailwindcss/typography')

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
    // prose and ring base used in your article/thread bodies
    'prose','dark:prose-invert','ring-1',
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
      // Nice defaults for Markdown in threads/posts
      typography: (theme) => ({
        DEFAULT: {
          css: {
            '--tw-prose-body': theme('colors.stone.800'),
            '--tw-prose-headings': theme('colors.stone.900'),
            '--tw-prose-links': theme('colors.red.600'),
            '--tw-prose-bullets': theme('colors.stone.400'),
            h1: { marginTop: '0', marginBottom: theme('spacing.4') },
            h2: { marginTop: theme('spacing.8'), marginBottom: theme('spacing.4') },
            h3: { marginTop: theme('spacing.6'), marginBottom: theme('spacing.3') },
            p:  { marginTop: theme('spacing.4'), marginBottom: theme('spacing.4') },
            ul: { marginTop: theme('spacing.4'), marginBottom: theme('spacing.4') },
            ol: { marginTop: theme('spacing.4'), marginBottom: theme('spacing.4') },
            li: { marginTop: theme('spacing.1'), marginBottom: theme('spacing.1') },
            pre: { padding: theme('spacing.4'), borderRadius: theme('borderRadius.xl') },
            code: { fontWeight: '600' },
          },
        },
        invert: {
          css: {
            '--tw-prose-body': theme('colors.stone.200'),
            '--tw-prose-headings': theme('colors.stone.100'),
            '--tw-prose-links': theme('colors.red.300'),
            '--tw-prose-bullets': theme('colors.stone.500'),
          },
        },
      }),
    },
  },
  plugins: [
    typography,
    function ({ addBase, theme }) {
      addBase({ html: { fontFamily: theme('fontFamily.orbitron') } })
    },
  ],
}