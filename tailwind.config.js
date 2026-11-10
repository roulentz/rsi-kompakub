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
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            // Palet KompakUB (diambil dari desain Figma). Pakai nama ini di semua halaman.
            colors: {
                navy: {
                    DEFAULT: '#203967', // navbar, tombol gelap, teks
                    dark: '#00236F',    // kartu info postingan (tile)
                    deep: '#12285A',    // panel kiri halaman login
                },
                brand: {
                    DEFAULT: '#F5B86D', // oranye utama (menu aktif, tombol utama)
                    dark: '#EE9F44',    // hover
                    soft: '#FDEBD0',    // latar lembut
                },
                lavender: '#D9E1FF', // logo, pencarian, avatar navbar
                muted: '#4A5D80',   // teks sekunder
                surface: {
                    DEFAULT: '#F4F4F4', // kartu dan sidebar
                    tint: '#EEF0FF',    // chip, input
                },
            },
            borderRadius: {
                kub: '0.875rem',
            },
        },
    },

    plugins: [forms],
};
