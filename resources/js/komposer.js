// Komposer postingan di Beranda: memilih kategori (Umum atau Info Lomba) menentukan kolom yang tampil.
// Cari Tim memakai dialog tersendiri (postingan/_form-cari-tim). Dipakai lewat <x-komposer-postingan>.
export default ({ kategori = 'Biasa', panjang = 0, aksi = {} } = {}) => ({
    kategori,
    panjang,
    maks: 1000,

    get urlAksi() {
        return aksi.biasa;
    },

    pilih(k) {
        this.kategori = k;
    },

    fokus() {
        this.$refs.teks.focus();
    },
});
