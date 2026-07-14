import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                blue: {
                    600: '#1e40af', // Brand primary Blue
                },
                sk: {
                    blue: '#1e40af',
                    dark: '#1e3a8a',
                    light: '#3b82f6',
                    surface: '#eff6ff',
                }
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                sk: '0 4px 6px -1px rgba(30, 64, 175, 0.08), 0 2px 4px -1px rgba(30, 64, 175, 0.04)',
                'sk-lg': '0 10px 15px -3px rgba(30, 64, 175, 0.1), 0 4px 6px -2px rgba(30, 64, 175, 0.05)',
            }
        },
    },

    plugins: [forms],
};
