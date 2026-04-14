/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './template-parts/**/*.php',
    './page-templates/**/*.php',
    './woocommerce/**/*.php',
    './inc/**/*.php',
    './assets/js/**/*.js',
    './src/**/*.css',
  ],
  // Safelist: classes only toggled dynamically by main.js must be preserved
  // because Tailwind's JIT cannot detect class additions from JavaScript.
  safelist: [
    'translate-x-full',
    'hidden',
  ],
  theme: {
    extend: {
      colors: {
        // Smart Pharmacy brand teal.
        brand: {
          DEFAULT: '#10c0a9',
          500: '#10c0a9',
          600: '#0da592',
        },
      },
      fontFamily: {
        sans: ['"Instrument Sans"', 'system-ui', 'sans-serif'],
        serif: ['"Playfair Display"', 'Georgia', 'serif'],
      },
      letterSpacing: {
        tightest: '-0.04em',
      },
    },
  },
  plugins: [],
}
