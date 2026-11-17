<?php

namespace Tests\Feature;

use App\Models\FotoPostingan;
use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// Tahap 3: foto sampai 10 per postingan, galeri, pencarian memakai kartu beranda, chip kategori dihapus.
class Tahap3Test extends TestCase
{
    use RefreshDatabase;

    private int $n = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('public');
    }

    private function profil(): ProfilMahasiswa
    {
        $this->n++;
        $u = User::create(['name' => "Mhs{$this->n}", 'tanggal_lahir' => '2004-01-01', 'email' => "m{$this->n}@student.ub.ac.id", 'no_telepon' => '0800', 'password' => 'rahasia123']);

        return ProfilMahasiswa::create(['user_id' => $u->id, 'jurusan' => 'Sistem Informasi', 'angkatan' => '2023']);
    }

    private function gambar(string $nama): UploadedFile
    {
        // PNG 1x1 piksel, tidak butuh ekstensi GD
        return UploadedFile::fake()->createWithContent($nama, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
    }

    public function test_postingan_umum_menyimpan_banyak_foto_berurutan(): void
    {
        $saya = $this->profil();

        $this->actingAs($saya->user)->post(route('post-biasa.store'), [
            'kategori' => 'Biasa', 'deskripsi' => 'Dokumentasi acara',
            'foto' => [$this->gambar('a.png'), $this->gambar('b.png'), $this->gambar('c.png')],
        ])->assertSessionHasNoErrors();

        $p = Postingan::first();
        $this->assertCount(3, $p->fotoPostingan);
        $this->assertSame($p->fotoPostingan->first()->path, $p->foto);
        $this->assertCount(3, $p->daftarFoto());
        foreach ($p->fotoPostingan as $f) {
            Storage::disk('public')->assertExists($f->path);
        }
    }

    public function test_foto_tunggal_cara_lama_tetap_diterima(): void
    {
        $saya = $this->profil();

        $this->actingAs($saya->user)->post(route('post-biasa.store'), [
            'kategori' => 'Biasa', 'deskripsi' => 'Satu foto', 'foto' => $this->gambar('a.png'),
        ])->assertSessionHasNoErrors();

        $this->assertCount(1, Postingan::first()->fotoPostingan);
    }

    public function test_lebih_dari_sepuluh_foto_ditolak(): void
    {
        $saya = $this->profil();
        $foto = [];
        for ($i = 1; $i <= 11; $i++) {
            $foto[] = $this->gambar("f{$i}.png");
        }

        $this->actingAs($saya->user)->post(route('post-biasa.store'), ['kategori' => 'Biasa', 'deskripsi' => 'Terlalu banyak', 'foto' => $foto])
            ->assertSessionHasErrors('foto');

        $this->assertSame(0, Postingan::count());
    }

    public function test_cari_tim_bisa_banyak_foto(): void
    {
        $saya = $this->profil();

        $this->actingAs($saya->user)->post('/postingan', [
            'nama_lomba' => 'IT Fest', 'waktu_perlombaan' => '2026-12-01', 'jumlah_anggota' => 3,
            'tag_skill' => 'UI/UX Design', 'deskripsi' => 'butuh front end',
            'foto' => [$this->gambar('a.png'), $this->gambar('b.png')],
        ])->assertSessionHasNoErrors();

        $this->assertCount(2, Postingan::first()->fotoPostingan);
    }

    public function test_postingan_lama_dengan_satu_kolom_foto_tetap_tampil(): void
    {
        $saya = $this->profil();
        $p = Postingan::create(['id_pembuat' => $saya->id_profil, 'kategori' => 'Biasa', 'deskripsi' => 'Lama', 'foto' => 'postingan/lama.png']);

        $this->assertSame(['/storage/postingan/lama.png'], $p->fresh()->daftarFoto());
    }

    public function test_feed_menampilkan_lapisan_sisa_foto_untuk_lebih_dari_empat(): void
    {
        $saya = $this->profil();
        $p = Postingan::create(['id_pembuat' => $saya->id_profil, 'kategori' => 'Biasa', 'deskripsi' => 'Banyak foto']);
        foreach (range(0, 5) as $i) {
            FotoPostingan::create(['id_postingan' => $p->id_postingan, 'path' => "postingan/f{$i}.png", 'urutan' => $i]);
        }

        $this->actingAs($saya->user)->get('/dashboard')->assertOk()->assertSee('+2');
    }

    public function test_menghapus_postingan_menghapus_foto_dan_berkasnya(): void
    {
        $saya = $this->profil();
        $this->actingAs($saya->user)->post(route('post-biasa.store'), ['kategori' => 'Biasa', 'deskripsi' => 'Hapus', 'foto' => [$this->gambar('a.png')]]);
        $p = Postingan::first();
        $path = $p->foto;

        $p->delete();

        $this->assertSame(0, FotoPostingan::count());
        Storage::disk('public')->assertMissing($path);
    }

    public function test_pencarian_memakai_kartu_yang_sama_dengan_beranda(): void
    {
        $saya = $this->profil();
        Postingan::create(['id_pembuat' => $saya->id_profil, 'kategori' => 'Cari Tim', 'nama_lomba' => 'IT Fest', 'waktu_perlombaan' => '2026-12-01', 'jumlah_anggota' => 3, 'tag_skill' => 'UI/UX Design', 'deskripsi' => 'butuh front end']);

        $this->actingAs($saya->user)->get(route('search.index', ['q' => 'IT Fest']))
            ->assertOk()
            ->assertSee('Slot Anggota')
            ->assertSee('Skill Required')
            ->assertSee('Lihat Detail');
    }

    public function test_kartu_tanpa_lencana_kategori_dan_selesai_berlabel_abu(): void
    {
        $saya = $this->profil();
        Postingan::create(['id_pembuat' => $saya->id_profil, 'kategori' => 'Cari Tim', 'nama_lomba' => 'Lomba Aktif', 'waktu_perlombaan' => '2026-12-01', 'jumlah_anggota' => 3, 'tag_skill' => 'Figma', 'deskripsi' => 'aktif']);
        Postingan::create(['id_pembuat' => $saya->id_profil, 'kategori' => 'Cari Tim', 'nama_lomba' => 'Lomba Usai', 'waktu_perlombaan' => '2026-01-01', 'jumlah_anggota' => 3, 'tag_skill' => 'Figma', 'deskripsi' => 'usai', 'status' => 'Selesai']);

        $html = $this->actingAs($saya->user)->get('/dashboard')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'Lomba telah selesai'));
        $this->assertDoesNotMatchRegularExpression('/kub-chip[^>]*>\s*(Cari Tim|Info Lomba)\s*</', $html);
    }

    public function test_komposer_tidak_lagi_memuat_teks_bantuan_tab(): void
    {
        $saya = $this->profil();

        $this->actingAs($saya->user)->get('/dashboard')->assertOk()
            ->assertDontSee('Postingan umum tampil di tab Semua')
            ->assertDontSee('Tampil di tab Info Lomba');
    }
}
