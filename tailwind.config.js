import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', 'Inter', ...defaultTheme.fontFamily.sans],
                serif: ['"DM Serif Display"', 'Playfair Display', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                brand: {
                    lime: '#cbf83e',
                    'lime-hover': '#b5e32c',
                    mint: '#e6fbf9',
                    teal: '#0891b2',
                    dark: '#111827',
                },
                bps: {
                    navy: {
                        50: '#f0f4f9',
                        100: '#e1e9f3',
                        200: '#c3d3e7',
                        300: '#a5bddb',
                        400: '#6992c3',
                        500: '#2d67ab',
                        600: '#0b2c59', // BPS Official Navy
                        700: '#092449',
                        800: '#071c39',
                        900: '#051429',
                    },
                    blue: {
                        50: '#f0f8ff',
                        100: '#e0f0fe',
                        500: '#0073b7', // BPS Statistic Blue
                        600: '#005f98',
                        700: '#004c7a',
                    },
                    green: {
                        50: '#f2f9f0',
                        100: '#e5f3e1',
                        500: '#6cba43', // BPS Green
                        600: '#529631',
                        700: '#3e7323',
                    },
                    orange: {
                        50: '#fef8f0',
                        100: '#fdf1e1',
                        500: '#f39c12', // BPS Orange Accent
                        600: '#d68409',
                        700: '#b06a04',
                    }
                }
            },
            keyframes: {
                'float-gentle': {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-6px)' },
                },
                'float-slow': {
                    '0%, 100%': { transform: 'translateY(0) rotate(0deg)' },
                    '50%': { transform: 'translateY(-8px) rotate(1deg)' },
                },
                'pulse-subtle': {
                    '0%, 100%': { opacity: '1', transform: 'scale(1)' },
                    '50%': { opacity: '0.85', transform: 'scale(1.03)' },
                },
                'shimmer': {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(100%)' },
                },
            },
            animation: {
                'float-gentle': 'float-gentle 4s ease-in-out infinite',
                'float-slow': 'float-slow 6s ease-in-out infinite',
                'pulse-subtle': 'pulse-subtle 3s ease-in-out infinite',
                'shimmer': 'shimmer 2.5s infinite',
            },
            boxShadow: {
                'glow-lime': '0 0 25px -5px rgba(203, 248, 62, 0.45)',
                'glow-teal': '0 0 25px -5px rgba(8, 145, 178, 0.35)',
                'card-hover': '0 20px 40px -15px rgba(15, 23, 42, 0.12)',
            }
        },
    },

    plugins: [forms, typography],
};