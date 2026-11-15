// Unggah gambar dengan pratinjau (seperti di Twitter): pilih atau seret gambar, lihat hasilnya,
// hapus yang salah sebelum dikirim. Dipakai lewat komponen <x-unggah-gambar>.
export default ({ multiple = false, maksMb = 2, maksFile = 20, webp = false } = {}) => ({
    multiple,
    files: [],
    items: [],
    galat: '',
    terseret: false,

    pilih(event) {
        this.tambah(Array.from(event.target.files));
    },

    jatuh(event) {
        this.terseret = false;
        this.tambah(Array.from(event.dataTransfer.files));
    },

    tambah(berkas) {
        this.galat = '';
        const diterima = multiple ? [...this.files] : [];

        for (const f of berkas) {
            if (!(webp ? ['image/jpeg', 'image/png', 'image/webp'] : ['image/jpeg', 'image/png']).includes(f.type)) {
                this.galat = `${f.name}: format harus ${webp ? 'JPG, PNG, atau WebP' : 'JPG atau PNG'}.`;
                continue;
            }
            if (f.size > maksMb * 1048576) {
                this.galat = `${f.name}: ukuran melebihi ${maksMb} MB.`;
                continue;
            }
            if (diterima.length >= maksFile) {
                this.galat = `Maksimal ${maksFile} gambar.`;
                break;
            }
            diterima.push(f);
            if (!multiple) {
                break;
            }
        }

        this.sinkron(diterima);
    },

    hapus(i) {
        this.galat = '';
        this.sinkron(this.files.filter((_, n) => n !== i));
    },

    // Menyamakan isi <input type="file"> dengan daftar yang tampil di pratinjau.
    sinkron(daftar) {
        const dt = new DataTransfer();
        daftar.forEach((f) => dt.items.add(f));
        this.$refs.input.files = dt.files;

        this.items.forEach((i) => URL.revokeObjectURL(i.url));
        this.files = daftar;
        this.items = daftar.map((f) => ({
            url: URL.createObjectURL(f),
            nama: f.name,
            ukuran: (f.size / 1048576).toFixed(2),
        }));
    },

    buka() {
        this.$refs.input.click();
    },
});
