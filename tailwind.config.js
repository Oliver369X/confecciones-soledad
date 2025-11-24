import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['var(--font-family)', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: 'var(--color-primary)',
                secondary: 'var(--color-secondary)',
                accent: 'var(--color-accent)',
                'theme-bg': 'var(--mode-bg-primary)',
                'theme-text': 'var(--mode-text-primary)',
                'theme-card': 'var(--mode-bg-secondary)',
                'theme-border': 'var(--mode-border)',
            },
            borderRadius: {
                DEFAULT: 'var(--radius)',
            },
        },
    },

    plugins: [forms],
};
