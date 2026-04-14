/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './template-parts/**/*.php',
    './woocommerce/**/*.php',
    './assets/js/**/*.js',
    './src/**/*.css',
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
