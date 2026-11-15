<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostinganDetailTest extends TestCase
{
    use RefreshDatabase;

    private int $n = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function profil(): ProfilMahasiswa
    {
        $this->n++;
        $u = User::create(['name' => "Mhs{$this->n}", 'tanggal_lahir' => '2004-01-01', 'email' => "m{$this->n}@student.ub.ac.id", 'no_telepon' => '0800', 'password' => 'rahasia123']);

        return ProfilMahasiswa::create(['user_id' => $u->id, 'jurusan' => 'Sistem Informasi', 'angkatan' => '2023', 'bio' => 'bio saya']);
    }

    private function buatPost(ProfilMahasiswa $pembuat, string $kategori = 'Cari Tim'): Postingan
    {
        return Postingan::create([
            'id_pembuat' => $pembuat->id_profil, 'kategori' => $kategori, 'nama_lomba' => 'Techpreneur 2026',
            'waktu_perlombaan' => '2026-12-01', 'tag_skill' => 'Research, UI Design', 'jumlah_anggota' => 4, 'deskripsi' => 'Cari teman satu tim',
        ]);
    }

    public function test_pembuat_melihat_blok_kelola_dan_tombol_selesai_aktif_setelah_ada_anggota(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);

        $this->actingAs($pembuat->user)->get(route('postingan.show', $p->id_postingan))
            ->assertOk()
            ->assertSee('Kelola Proyek')
            ->assertSee('Lihat Pelamar')
            ->assertSee('Rekomendasi Kandidat')
            ->assertDontSee('Tandai Proyek Selesai?');

        Lamaran::create(['id_postingan' => $p->id_postingan, 'id_pelamar' => $this->profil()->id_profil, 'status' => 'Diterima']);

        $this->actingAs($pembuat->user)->get(route('postingan.show', $p->id_postingan))
            ->assertOk()
            ->assertSee('Tandai Proyek Selesai?')
            ->assertSee('1 Mahasiswa')
            ->assertSee(route('postingan.selesai', $p->id_postingan));
    }

    public function test_pembuat_tidak_lagi_melihat_tombol_setelah_selesai(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        Lamaran::create(['id_postingan' => $p->id_postingan, 'id_pelamar' => $this->profil()->id_profil, 'status' => 'Diterima']);
        $p->update(['status' => 'Selesai']);

        $this->actingAs($pembuat->user)->get(route('postingan.show', $p->id_postingan))
            ->assertOk()
            ->assertSee('Proyek selesai')
            ->assertDontSee('Tandai Proyek Selesai?');
    }

    public function test_mahasiswa_lain_melihat_apply_tanpa_blok_kelola(): void
    {
        $p = $this->buatPost($this->profil());
        $lain = $this->profil();

        $this->actingAs($lain->user)->get(route('postingan.show', $p->id_postingan))
            ->assertOk()
            ->assertSee('Techpreneur 2026')
            ->assertSee('Research')
            ->assertSee('Apply')
            ->assertSee(route('lamaran.store', $p->id_postingan))
            ->assertDontSee('Kelola Proyek');
    }

    public function test_yang_sudah_melamar_melihat_status_bukan_tombol_apply(): void
    {
        $p = $this->buatPost($this->profil());
        $lain = $this->profil();
        Lamaran::create(['id_postingan' => $p->id_postingan, 'id_pelamar' => $lain->id_profil, 'status' => 'Menunggu Konfirmasi']);

        $this->actingAs($lain->user)->get(route('postingan.show', $p->id_postingan))
            ->assertOk()
            ->assertSee('Menunggu Konfirmasi')
            ->assertDontSee(route('lamaran.store', $p->id_postingan));
    }

    public function test_postingan_selesai_tidak_menawarkan_apply(): void
    {
        $p = $this->buatPost($this->profil());
        $p->update(['status' => 'Selesai']);

        $this->actingAs($this->profil()->user)->get(route('postingan.show', $p->id_postingan))
            ->assertOk()
            ->assertSee('Proyek selesai')
            ->assertDontSee(route('lamaran.store', $p->id_postingan));
    }

    public function test_postingan_kategori_biasa_tampil_tanpa_error(): void
    {
        $pembuat = $this->profil();
        $p = Postingan::create(['id_pembuat' => $pembuat->id_profil, 'kategori' => 'Biasa', 'deskripsi' => 'Halo semua']);

        $this->actingAs($pembuat->user)->get(route('postingan.show', $p->id_postingan))
            ->assertOk()
            ->assertSee('Halo semua')
            ->assertDontSee('Kelola Proyek')
            ->assertDontSee('Apply');
    }
}
