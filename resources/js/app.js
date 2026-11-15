import './bootstrap';

import Alpine from 'alpinejs';
import komposer from './komposer';
import unggahGambar from './unggah-gambar';

window.Alpine = Alpine;

Alpine.data('komposer', komposer);
Alpine.data('unggahGambar', unggahGambar);

Alpine.start();
