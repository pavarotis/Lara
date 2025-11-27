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
                sans: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Primary brand color - warm amber/orange
                primary: {
                    DEFAULT: '#D97706',
                    50: '#FEF3C7',
                    100: '#FDE68A',
                    200: '#FCD34D',
                    300: '#FBBF24',
                    400: '#F59E0B',
                    500: '#D97706',
                    600: '#B45309',
                    700: '#92400E',
                    800: '#78350F',
                    900: '#451A03',
                },
                // Accent color - teal
                accent: {
                    DEFAULT: '#0D9488',
                    50: '#CCFBF1',
                    100: '#99F6E4',
                    200: '#5EEAD4',
                    300: '#2DD4BF',
                    400: '#14B8A6',
                    500: '#0D9488',
                    600: '#0F766E',
                    700: '#115E59',
                    800: '#134E4A',
                    900: '#042F2E',
                },
                // Surface colors
                surface: '#FAFAF9',
                content: '#1C1917',
            },
        },
    },

    plugins: [forms],
};
