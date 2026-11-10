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
                    DEFAULT: '#1E3A6D', // navbar, tombol gelap
                    dark: '#0F2A68',    // kartu info postingan
                    deep: '#12285A',    // panel kiri halaman login
                },
                brand: {
                    DEFAULT: '#F5B364', // oranye utama (menu aktif, tombol utama)
                    dark: '#EE9F44',    // hover
                    soft: '#FDEBD0',    // latar lembut
                },
                surface: {
                    DEFAULT: '#EEEEEE', // kartu dan sidebar
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
