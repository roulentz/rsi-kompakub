// Galeri foto postingan: kisi ringkas di feed, klik untuk membuka penampil layar penuh
// (panah, papan ketik, dan geser jari/mouse untuk pindah foto). Dipakai lewat <x-galeri-foto>.
export default (fotos = []) => ({
    fotos,
    buka: false,
    i: 0,
    awal: null,

    tampil(n) {
        this.i = n;
        this.buka = true;
        document.body.classList.add('overflow-hidden');
    },

    tutup() {
        this.buka = false;
        document.body.classList.remove('overflow-hidden');
    },

    geser(arah) {
        this.i = (this.i + arah + this.fotos.length) % this.fotos.length;
    },

    mulaiSentuh(e) {
        this.awal = e.clientX;
    },

    akhiriSentuh(e) {
        if (this.awal === null) return;
        const selisih = e.clientX - this.awal;
        this.awal = null;
        if (Math.abs(selisih) > 50 && this.fotos.length > 1) {
            this.geser(selisih < 0 ? 1 : -1);
        }
    },
});
