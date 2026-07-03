import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            colors: {
                // Surface
                surface: "#f9fafb",
                "surface-dim": "#dbd9e1",
                "surface-bright": "#f9fafb",
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
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
            },

            fontSize: {
                // 48px -> 3rem, 56px -> 3.5rem
                "display-lg": [
                    "3rem",
                    {
                        lineHeight: "3.5rem",
                        letterSpacing: "-0.02em",
                        fontWeight: "700",
                    },
                ],
                // 32px -> 2rem, 40px -> 2.5rem
                "headline-lg": [
                    "2rem",
                    {
                        lineHeight: "2.5rem",
                        letterSpacing: "-0.01em",
                        fontWeight: "600",
                    },
                ],
                // 28px -> 1.75rem, 36px -> 2.25rem
                "headline-lg-mobile": [
                    "1.75rem",
                    { lineHeight: "2.25rem", fontWeight: "600" },
                ],
                // 24px -> 1.5rem, 32px -> 2rem
                "headline-md": [
                    "1.5rem",
                    { lineHeight: "2rem", fontWeight: "600" },
                ],
                // 20px -> 1.25rem, 28px -> 1.75rem
                "title-lg": [
                    "1.25rem",
                    { lineHeight: "1.75rem", fontWeight: "500" },
                ],
                // 18px -> 1.125rem, 24px -> 1.5rem
                "title-md": [
                    "1.125rem",
                    { lineHeight: "1.5rem", fontWeight: "600" },
                ],
                // 16px -> 1rem, 24px -> 1.5rem
                "title-sm": [
                    "1rem",
                    { lineHeight: "1.5rem", fontWeight: "600" },
                ],
                // 16px -> 1rem, 24px -> 1.5rem
                "body-lg": [
                    "1rem",
                    { lineHeight: "1.5rem", fontWeight: "400" },
                ],
                // 14px -> 0.875rem, 20px -> 1.25rem
                "body-md": [
                    "0.875rem",
                    { lineHeight: "1.25rem", fontWeight: "400" },
                ],
                // 12px -> 0.75rem, 16px -> 1rem
                "body-sm": [
                    "0.75rem",
                    { lineHeight: "1rem", fontWeight: "400" },
                ],
                // 12px -> 0.75rem, 16px -> 1rem
                "label-md": [
                    "0.75rem",
                    { lineHeight: "1rem", fontWeight: "600" },
                ],
                // 11px -> 0.6875rem, 14px -> 0.875rem
                caption: [
                    "0.6875rem",
                    { lineHeight: "0.875rem", fontWeight: "400" },
                ],
            },

            borderRadius: {
                sm: "0.25rem", // 4px
                DEFAULT: "0.5rem", // 8px
                md: "0.75rem", // 12px
                lg: "1rem", // 16px
                xl: "1.5rem", // 24px
                full: "9999px",
            },

            spacing: {
                xs: "0.25rem", // 4px
                sm: "0.5rem", // 8px
                md: "1rem", // 16px
                lg: "1.5rem", // 24px
                xl: "2.5rem", // 40px
            },

            gap: (theme) => theme("spacing"),
            padding: (theme) => theme("spacing"),
            margin: (theme) => theme("spacing"),

            maxWidth: {
                container: "80rem", // 1280px
            },

            boxShadow: {
                card: "0px 4px 12px rgba(26, 35, 126, 0.05)",
            },

            width: {
                sidebar: "16.25rem", // 260px
            },
        },
    },
    plugins: [forms],
};