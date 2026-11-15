// Unggah gambar dengan pratinjau (seperti di Twitter): pilih atau seret gambar, lihat hasilnya,
// hapus yang salah sebelum dikirim. Dipakai lewat komponen <x-unggah-gambar>.
const BINGKAI = 280; // ukuran bingkai crop di layar (px)
const HASIL = 512;   // ukuran foto hasil crop (px, persegi)

export default ({ multiple = false, maksMb = 2, maksFile = 20, webp = false, potong = false } = {}) => ({
    multiple,
    files: [],
    items: [],
    galat: '',
    terseret: false,

    // Status crop foto (hanya dipakai jika potong = true).
    crop: { buka: false, img: null, src: '', nama: '', skala: 1, min: 1, x: 0, y: 0, seret: null },

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

        // Foto profil: buka editor crop dulu, baru dimasukkan ke input.
        if (potong && diterima.length) {
            this.bukaCrop(diterima[0]);
            return;
        }

        this.sinkron(diterima);
    },

    // ---- Crop & atur posisi foto ----
    bukaCrop(file) {
        const img = new Image();
        img.onload = () => {
            const min = Math.max(BINGKAI / img.naturalWidth, BINGKAI / img.naturalHeight);
            this.crop = {
                buka: true, img, src: img.src, nama: file.name, min,
                skala: min,
                x: (BINGKAI - img.naturalWidth * min) / 2,
                y: (BINGKAI - img.naturalHeight * min) / 2,
                seret: null,
            };
        };
        img.src = URL.createObjectURL(file);
        this.$refs.input.value = '';
    },

    batasi() {
        const c = this.crop;
        c.x = Math.min(0, Math.max(BINGKAI - c.img.naturalWidth * c.skala, c.x));
        c.y = Math.min(0, Math.max(BINGKAI - c.img.naturalHeight * c.skala, c.y));
    },

    zoom(nilai) {
        const c = this.crop;
        const lama = c.skala;
        // Zoom mengarah ke titik tengah bingkai.
        const px = (BINGKAI / 2 - c.x) / lama;
        const py = (BINGKAI / 2 - c.y) / lama;
        c.skala = Math.max(c.min, parseFloat(nilai));
        c.x = BINGKAI / 2 - px * c.skala;
        c.y = BINGKAI / 2 - py * c.skala;
        this.batasi();
    },

    mulaiSeret(e) {
        e.target.setPointerCapture?.(e.pointerId);
        this.crop.seret = { px: e.clientX, py: e.clientY, x: this.crop.x, y: this.crop.y };
    },

    seret(e) {
        const s = this.crop.seret;
        if (!s) return;
        this.crop.x = s.x + (e.clientX - s.px);
        this.crop.y = s.y + (e.clientY - s.py);
        this.batasi();
    },

    akhiriSeret() {
        this.crop.seret = null;
    },

    batalCrop() {
        URL.revokeObjectURL(this.crop.src);
        this.crop = { ...this.crop, buka: false, img: null, src: '', seret: null };
    },

    simpanCrop() {
        const c = this.crop;
        const kanvas = document.createElement('canvas');
        kanvas.width = kanvas.height = HASIL;
        const ctx = kanvas.getContext('2d');
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, HASIL, HASIL);
        // Bagian gambar asli yang berada di dalam bingkai.
        ctx.drawImage(c.img, -c.x / c.skala, -c.y / c.skala, BINGKAI / c.skala, BINGKAI / c.skala, 0, 0, HASIL, HASIL);

        kanvas.toBlob((blob) => {
            const nama = c.nama.replace(/\.[^.]+$/, '') + '.jpg';
            this.sinkron([new File([blob], nama, { type: 'image/jpeg' })]);
            this.batalCrop();
        }, 'image/jpeg', 0.9);
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

    bingkai: BINGKAI,

    buka() {
        this.$refs.input.click();
    },
});
