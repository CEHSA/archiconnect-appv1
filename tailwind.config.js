import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'architimex-primary': '#3B8A8A',
                'architimex-primary-darker': '#2D7A7A',
                'architimex-sidebar': '#2D5C5C',
                'architimex-lightbg': '#F4F7F6',
                // Keep old names for backward compatibility
                'archiconnect-primary': '#3B8A8A',
                'archiconnect-primary-darker': '#2D7A7A',
                'archiconnect-sidebar': '#2D5C5C',
                'archiconnect-lightbg': '#F4F7F6',
            },
        },
    },

    plugins: [forms],
};
