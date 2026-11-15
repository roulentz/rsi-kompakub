import './bootstrap';

import Alpine from 'alpinejs';
import komposer from './komposer';
import unggahGambar from './unggah-gambar';
import pilihSkill from './pilih-skill';

window.Alpine = Alpine;

Alpine.data('komposer', komposer);
Alpine.data('unggahGambar', unggahGambar);
Alpine.data('pilihSkill', pilihSkill);

Alpine.start();
