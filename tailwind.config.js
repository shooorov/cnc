import defaultTheme from 'tailwindcss/defaultTheme';
import colors from 'tailwindcss/colors';
import forms from '@tailwindcss/forms';
import aspectRatio from '@tailwindcss/aspect-ratio';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './app/Http/Resources/*.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: colors.teal,
                secondary: colors.orange,
            },
			animation: {
				wiggle: 'vibrate .2s ease-in-out infinite',
			},
			keyframes: {
				vibrate: {
				  '0%, 100%': { transform: 'rotate(0deg)' },
				  '25%': { transform: 'rotate(-7deg)' },
				  '75%': { transform: 'rotate(7deg)' },
				}
			}
        },
    },

    plugins: [forms, aspectRatio],
};
