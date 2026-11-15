<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Portofolio;
use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// Menguji perbaikan tampilan: kategori saat membuat postingan, foto yang tampil, profil lengkap.
class TampilanDanPostinganTest extends TestCase
{
    use RefreshDatabase;

    private int $n = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('public');
    }

    private function profil(string $nama = null): ProfilMahasiswa
    {
        $this->n++;
        $u = User::create(['name' => $nama ?? "U{$this->n}", 'tanggal_lahir' => '2004-01-01', 'email' => "t{$this->n}@student.ub.ac.id", 'no_telepon' => '0800', 'password' => 'rahasia123']);

        return ProfilMahasiswa::create(['user_id' => $u->id, 'jurusan' => 'Sistem Informasi', 'angkatan' => '2023']);
    }

    private function gambar(string $nama = 'foto.png'): UploadedFile
    {
        // PNG 1x1 piksel, tidak butuh ekstensi GD
        return UploadedFile::fake()->createWithContent($nama, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
    }

    public function test_alamat_gambar_relatif_agar_tampil_di_host_dan_port_mana_pun(): void
    {
        $this->assertSame('/storage/portofolio/a.png', Storage::disk('public')->url('portofolio/a.png'));
    }

    public function test_beranda_tanpa_label_kategori_aktif_dan_tanpa_lencana_biasa(): void
    {
        $saya = $this->profil('Saya');
        Postingan::create(['id_pembuat' => $saya->id_profil, 'kategori' => 'Biasa', 'deskripsi' => 'Halo kampus, ini postingan umum']);
        Postingan::create(['id_pembuat' => $saya->id_profil, 'kategori' => 'Cari Tim', 'nama_lomba' => 'Techpreneur 2026', 'waktu_perlombaan' => '2026-10-10', 'jumlah_anggota' => 3, 'tag_skill' => 'Figma, Public Speaking', 'deskripsi' => 'Cari anggota']);

        $html = $this->actingAs($saya->user)->get('/dashboard')->assertOk()->getContent();

        $this->assertStringNotContainsString('Kategori aktif', $html);
        $this->assertStringContainsString('Halo kampus, ini postingan umum', $html);
        $this->assertStringContainsString('Techpreneur 2026', $html);
        $this->assertStringContainsString('Skill Required', $html);
        // lencana "Biasa" dihapus, "Cari Tim" tetap ada pada kartu
        $this->assertDoesNotMatchRegularExpression('/kub-chip[^>]*>\s*Biasa\s*</', $html);
        $this->assertMatchesRegularExpression('/kub-chip[^>]*>\s*Cari Tim\s*</', $html);
    }

    public function test_komposer_menyediakan_pilihan_kategori_dan_pratinjau_foto(): void
    {
        $saya = $this->profil('Saya');
        Skill::create(['nama_skill' => 'Figma']);

        $html = $this->actingAs($saya->user)->get('/dashboard')->assertOk()->getContent();

        $this->assertStringContainsString('id="buat-postingan"', $html);
        foreach (['Umum', 'Cari Tim', 'Info Lomba'] as $label) {
            $this->assertStringContainsString($label, $html);
        }
        $this->assertStringContainsString('unggahGambar(', $html);
        $this->assertStringContainsString('Tambahkan Foto', $html);
        // dialog Cari Tim milik SH ikut terpasang sebagai satu-satunya formulir Cari Tim
        $this->assertSame(1, substr_count($html, 'Form Buat Postingan Cari Tim'));
    }

    public function test_posting_umum_dengan_foto_tersimpan_dan_kembali_ke_beranda(): void
    {
        $saya = $this->profil('Saya');

        $this->actingAs($saya->user)->post(route('post-biasa.store'), [
            'kategori' => 'Biasa', 'deskripsi' => 'Halo semua', 'foto' => $this->gambar(),
        ])->assertRedirect(route('dashboard', ['kategori' => 'Semua']))->assertSessionHas('status');

        $p = Postingan::first();
        $this->assertSame('Biasa', $p->kategori);
        Storage::disk('public')->assertExists($p->foto);
    }

    public function test_info_lomba_wajib_nama_dan_tanggal_lalu_tersimpan(): void
    {
        $saya = $this->profil('Saya');

        $this->actingAs($saya->user)->post(route('post-biasa.store'), ['kategori' => 'Info Lomba', 'deskripsi' => 'Ada lomba baru'])
            ->assertSessionHasErrors(['nama_lomba', 'waktu_perlombaan']);
        $this->assertSame(0, Postingan::count());

        $this->actingAs($saya->user)->post(route('post-biasa.store'), [
            'kategori' => 'Info Lomba', 'deskripsi' => 'Ada lomba baru', 'nama_lomba' => 'Gemastik', 'waktu_perlombaan' => '2026-12-01',
        ])->assertRedirect(route('dashboard', ['kategori' => 'Semua']));

        $this->assertDatabaseHas('postingan', ['kategori' => 'Info Lomba', 'nama_lomba' => 'Gemastik']);
    }

    public function test_cari_tim_dengan_foto_tersimpan_dan_membuka_halaman_detail(): void
    {
        $saya = $this->profil('Saya');

        $respons = $this->actingAs($saya->user)->post(route('postingan.store'), [
            'nama_lomba' => 'Techpreneur 2026', 'waktu_perlombaan' => '2027-01-10', 'jumlah_anggota' => 3,
            'tag_skill' => 'Figma, Public Speaking', 'deskripsi' => 'Cari anggota tim', 'foto' => $this->gambar(),
        ]);

        $p = Postingan::first();
        $respons->assertRedirect(route('postingan.show', $p->id_postingan))->assertSessionHas('id_postingan');
        $this->assertSame('Cari Tim', $p->kategori);
        Storage::disk('public')->assertExists($p->foto);
    }

    public function test_foto_bukan_gambar_ditolak(): void
    {
        $saya = $this->profil('Saya');

        $this->actingAs($saya->user)->post(route('post-biasa.store'), [
            'deskripsi' => 'Halo', 'foto' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors('foto');
    }

    public function test_profil_lengkap_menampilkan_portofolio_dengan_alamat_gambar_relatif(): void
    {
        $saya = $this->profil('Saya');
        Portofolio::create(['id_profil' => $saya->id_profil, 'konten' => 'portofolio/a.png', 'urutan_halaman' => 1, 'ukuran_file' => 0.2, 'tautan_eksternal' => 'https://figma.com/x']);

        $html = $this->actingAs($saya->user)->get(route('profil.lihat', $saya))->assertOk()->getContent();

        // alamat ada di dalam JSON untuk slider, jadi garis miringnya ter-escape (\/)
        $this->assertMatchesRegularExpression('#/storage\\\\*/portofolio\\\\*/a\.png#', $html);
        $this->assertStringContainsString('Riwayat Kolaborasi Kampus', $html);
        $this->assertStringContainsString('Rekomendasi Rekan Tim', $html);
        $this->assertStringContainsString('Edit Profil', $html);
    }

    public function test_halaman_profil_orang_lain_tanpa_tombol_edit(): void
    {
        $saya = $this->profil('Saya');
        $lain = $this->profil('Maya Anggraini');

        $html = $this->actingAs($saya->user)->get(route('profil.lihat', $lain))->assertOk()->getContent();

        $this->assertStringContainsString('Maya Anggraini', $html);
        $this->assertStringNotContainsString('Edit Profil', $html);
    }

    public function test_halaman_edit_dan_portofolio_memakai_unggah_dengan_pratinjau(): void
    {
        $saya = $this->profil('Saya');

        $this->actingAs($saya->user)->get(route('profil.edit'))->assertOk()->assertSee('unggahGambar(', false);
        $this->actingAs($saya->user)->get(route('portofolio.edit'))->assertOk()->assertSee('unggahGambar(', false);
    }

    public function test_foto_profil_tampil_pada_avatar_dengan_alamat_relatif(): void
    {
        $saya = $this->profil('Saya');
        $saya->update(['foto' => 'profil/saya.png']);

        $html = $this->actingAs($saya->user)->get('/dashboard')->assertOk()->getContent();

        $this->assertStringContainsString('src="/storage/profil/saya.png"', $html);
    }

    public function test_sidebar_tidak_dibungkus_container_terpusat(): void
    {
        $saya = $this->profil('Saya');

        $html = $this->actingAs($saya->user)->get('/dashboard')->assertOk()->getContent();

        // sidebar menempel ke tepi kiri: tidak ada pembungkus lebar maksimum 1440px lagi
        $this->assertStringNotContainsString('max-w-[1440px]', $html);
        $this->assertStringContainsString('Menu utama', $html);
    }
}
