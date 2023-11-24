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

                    "verd1": "#84f5a4",
                    "verd2": "#61FF7E",
                    "verd3": "#5EEB5B",
                    "verd4": "#62AB37",
                    "verd5": "#4E702E",
                    "verd6": "#393424",
                
				
			},

        },
    },


    plugins: [forms],
};
