// Pemilih skill dengan kolom cari: yang tampil hanya skill terpilih dan rekomendasi, sisanya muncul lewat pencarian.
// Dipakai lewat <x-pilih-skill>. mode "id" mengirim skills[] (id_skill), mode "nama" mengirim satu isian bernilai nama dipisah koma.
const normal = (t) => String(t).toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();

export default ({ daftar = [], dipilih = [], populer = [], mode = 'id', batas = 16 } = {}) => ({
    daftar: daftar.map((s) => ({ ...s, n: normal(s.nama) })),
    dipilih: dipilih.map(String),
    populer,
    mode,
    batas,
    q: '',

    kunci(s) {
        return this.mode === 'nama' ? s.nama : String(s.id);
    },

    // Skill terpilih, urutannya sesuai urutan memilih.
    get terpilih() {
        return this.dipilih
            .map((k) => this.daftar.find((s) => this.kunci(s) === k))
            .filter(Boolean);
    },

    get cocok() {
        const q = normal(this.q);
        const belum = (s) => !this.dipilih.includes(this.kunci(s));
        if (!q) {
            return this.populer
                .map((nama) => this.daftar.find((s) => s.nama === nama))
                .filter((s) => s && belum(s));
        }
        return this.daftar
            .filter((s) => belum(s) && s.n.includes(q))
            .sort((a, b) => (b.n.startsWith(q) - a.n.startsWith(q)) || a.nama.localeCompare(b.nama));
    },

    // Batasi jumlah chip agar halaman tetap ringkas.
    get hasil() {
        return this.cocok.slice(0, this.q.trim() ? 30 : this.batas);
    },

    pilih(s) {
        const k = this.kunci(s);
        if (!this.dipilih.includes(k)) {
            this.dipilih.push(k);
        }
        this.q = '';
        this.$refs.cari?.focus();
    },

    lepas(s) {
        const k = this.kunci(s);
        this.dipilih = this.dipilih.filter((x) => x !== k);
    },

    // Enter memilih hasil teratas (dan tidak mengirim formulir).
    pilihPertama() {
        if (this.q.trim() && this.hasil.length) {
            this.pilih(this.hasil[0]);
        }
    },
});
