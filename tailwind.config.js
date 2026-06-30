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
            colors: {
                // Surface
                surface: "#fbf8ff",
                "surface-dim": "#dbd9e1",
                "surface-bright": "#fbf8ff",
                "surface-container-lowest": "#ffffff",
                "surface-container-low": "#f5f2fb",
                "surface-container": "#efecf5",
                "surface-container-high": "#eae7ef",
                "surface-container-highest": "#e4e1ea",
                "on-surface": "#1b1b21",
                "on-surface-variant": "#454652",
                "inverse-surface": "#303036",
                "inverse-on-surface": "#f2eff8",

                // Outline
                outline: "#767683",
                "outline-variant": "#c6c5d4",

                // Primary
                primary: "#000666",
                "on-primary": "#ffffff",
                "primary-container": "#1a237e",
                "on-primary-container": "#8690ee",
                "inverse-primary": "#bdc2ff",
                "surface-tint": "#4c56af",
                "primary-fixed": "#e0e0ff",
                "primary-fixed-dim": "#bdc2ff",
                "on-primary-fixed": "#000767",
                "on-primary-fixed-variant": "#343d96",

                // Secondary
                secondary: "#526069",
                "on-secondary": "#ffffff",
                "secondary-container": "#d3e2ec",
                "on-secondary-container": "#56656e",
                "secondary-fixed": "#d6e5ef",
                "secondary-fixed-dim": "#bac9d3",
                "on-secondary-fixed": "#0f1d25",
                "on-secondary-fixed-variant": "#3b4951",

                // Tertiary
                tertiary: "#191b1c",
                "on-tertiary": "#ffffff",
                "tertiary-container": "#2e3031",
                "on-tertiary-container": "#979799",
                "tertiary-fixed": "#e2e2e3",
                "tertiary-fixed-dim": "#c6c6c7",
                "on-tertiary-fixed": "#1a1c1d",
                "on-tertiary-fixed-variant": "#454748",

                // Semantic
                error: "#ba1a1a",
                "on-error": "#ffffff",
                "error-container": "#ffdad6",
                "on-error-container": "#93000a",
                success: "#16a34a",
                warning: "#D97706",

                // Background
                background: "#fbf8ff",
                "on-background": "#1b1b21",
                "surface-variant": "#e4e1ea",
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
