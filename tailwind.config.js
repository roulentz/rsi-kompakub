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
                jakarta: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                navy: {
                    DEFAULT: '#203967',
                    dark:    '#00236F',   /* --tile di mockup */
                    deep:    '#12285A',   /* variasi gelap logo / footer */
                },
                brand: {
                    DEFAULT: '#F5B86D',   /* --orange */
                    dark:    '#E8A24F',
                    soft:    '#FDEBD0',
                },
                surface: {
                    DEFAULT: '#F4F4F4',   /* --grey */
                    tint:    '#EFF4FF',   /* --light */
                },
                lavender: '#D9E1FF',      /* --lavender */
                muted:    '#4A5D80',      /* --muted */
            },

            borderRadius: {
                'kub':  '5px',   /* kartu postingan & box */
                'kub-lg': '10px', /* inset putih, tile, compose, desc */
                'kub-xl': '15px', /* sidebar nav-item */
                'pill': '30px',  /* tombol pill, filter bar */
                'full': '9999px',
            },

            spacing: {
                'nav-h':   '85px',   /* tinggi navbar */
                'side-w':  '360px',  /* lebar sidebar */
                'feed-w':  '637px',  /* lebar feed */
                'inbox-w': '279px',  /* lebar inbox */
            },

            fontSize: {
                'logo':  ['29px', { lineHeight: '44px', fontWeight: '800' }],
                'nav':   ['21px', { lineHeight: '32px', fontWeight: '500' }],
                'title': ['22px', { lineHeight: '33px', fontWeight: '700' }],
                'post':  ['20px', { lineHeight: '30px', fontWeight: '700' }],
                'body':  ['16px', { lineHeight: '24px', fontWeight: '500' }],
                'sub':   ['11px', { lineHeight: '16px', fontWeight: '500' }],
                'chip':  ['7px',  { lineHeight: '10px', fontWeight: '500' }],
                'xs':    ['10px', { lineHeight: '14px' }],
            },
        },
    },

    plugins: [forms],
};
