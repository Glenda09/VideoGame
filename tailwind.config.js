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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Chakra Petch', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                neon: '0 0 25px rgba(236, 72, 153, 0.35)',
            },
            colors: {
                brand: {
                    pink: '#ec4899',
                    purple: '#8b5cf6',
                    slate: '#0f172a',
                },
            },
        },
    },

    plugins: [forms],
};
