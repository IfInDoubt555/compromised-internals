// postcss.config.cjs
const tailwindcss = require('@tailwindcss/postcss')
const autoprefixer = require('autoprefixer')

// require the PurgeCSS plugin; if it was published as an ES module, grab its `.default`
const purgecssPkg = require('@fullhuman/postcss-purgecss')
const purgecss = purgecssPkg.default || purgecssPkg

const isProduction = process.env.NODE_ENV === 'production'

// configure PurgeCSS
const purgeCssPlugin = purgecss({
  // 1) look through all Blade, JS, and Vue files
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  // 2) whitelist any classes that appear only dynamically
  safelist: [
    /^bg-/,
    /^text-/,
    'fade-in',
    'active',
    // add any others you generate at runtime…
  ],
  // 3) the default extractor handles Tailwind’s utility syntax
  defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
})

module.exports = {
  plugins: [
    tailwindcss(),
    autoprefixer(),
    // only include PurgeCSS when in production
    ...(isProduction ? [purgeCssPlugin] : []),
  ],
}