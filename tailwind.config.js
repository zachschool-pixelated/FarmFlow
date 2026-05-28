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
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                farm: {
                    50:  '#f0fdf4',
                    100: '#d8f3dc',
                    200: '#b7e4c7',
                    300: '#95d5b2',
                    400: '#74c69d',
                    500: '#52b788',
                    600: '#40916c',
                    700: '#2d6a4f',
                    800: '#1b4332',
                    900: '#0b2618',
                },
                earth: {
                    50:  '#fdf8f0',
                    100: '#f5e6d0',
                    200: '#e6ccb2',
                    300: '#ddb892',
                    400: '#c49a6c',
                    500: '#a67c52',
                    600: '#7f5539',
                    700: '#6b3f2a',
                    800: '#4a2c1a',
                    900: '#2c1a0e',
                },
                cream: {
                    50:  '#fefdf5',
                    100: '#fefae0',
                    200: '#fef3c7',
                },
            },
        },
    },

    plugins: [forms],
};
