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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['"Playfair Display"', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                // Palette Antika (dark luxe) extraite du site existant.
                antika: {
                    ink: '#0c0a09', // fond quasi noir
                    panel: '#16110e', // panneaux / cartes légèrement plus clairs
                    copper: '#d9551f', // accent cuivre/orange (eyebrows, soulignés)
                    coral: '#e0654f', // boutons d'action (CTA)
                    cream: '#f5efcf', // crème/or du logo et highlights
                },
                // Festival 1912 (page /sponsors, temporaire) : rouge albanais.
                festival: {
                    DEFAULT: '#c8102e',
                    dark: '#a00d25',
                    light: '#f0596f',
                },
            },
        },
    },

    plugins: [forms],
};
