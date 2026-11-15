<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\RiwayatKolaborasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostinganSelesaiTest extends TestCase
{
    use RefreshDatabase;

    private int $n = 0;

    private function profil(): ProfilMahasiswa
    {
        $this->n++;
        $u = User::create(['name' => "U{$this->n}", 'tanggal_lahir' => '2004-01-01', 'email' => "u{$this->n}@student.ub.ac.id", 'no_telepon' => '0800', 'password' => 'rahasia123']);

        return ProfilMahasiswa::create(['user_id' => $u->id, 'jurusan' => 'Sistem Informasi', 'angkatan' => '2023', 'bio' => 'bio saya']);
    }

    private function buatPost(ProfilMahasiswa $pembuat, string $kategori = 'Cari Tim'): Postingan
    {
        return Postingan::create(['id_pembuat' => $pembuat->id_profil, 'kategori' => $kategori, 'nama_lomba' => 'Techpreneur 2026', 'tag_skill' => 'Research', 'jumlah_anggota' => 4, 'deskripsi' => 'd']);
    }

    private function lamar(Postingan $p, ProfilMahasiswa $pelamar, string $status): Lamaran
    {
        return Lamaran::create(['id_postingan' => $p->id_postingan, 'id_pelamar' => $pelamar->id_profil, 'status' => $status]);
    }

    public function test_pembuat_menandai_selesai_dan_riwayat_tercatat(): void
    {
        $pembuat = $this->profil();
        $diterima = $this->profil();
        $ditolak = $this->profil();
        $menunggu = $this->profil();
        $p = $this->buatPost($pembuat);
        $this->lamar($p, $diterima, 'Diterima');
        $this->lamar($p, $ditolak, 'Ditolak');
        $this->lamar($p, $menunggu, 'Menunggu Konfirmasi');

        $this->actingAs($pembuat->user)
            ->post(route('postingan.selesai', $p->id_postingan))
            ->assertSessionHas('status', 'Proyek berhasil ditandai selesai');

        $this->assertSame('Selesai', $p->fresh()->status);
        $this->assertSame(2, RiwayatKolaborasi::count());
        $this->assertDatabaseHas('riwayat_kolaborasi', ['id_profil' => $pembuat->id_profil, 'id_postingan' => $p->id_postingan, 'peran' => 'Pembuat Tim', 'judul_proyek' => 'Techpreneur 2026']);
        $this->assertDatabaseHas('riwayat_kolaborasi', ['id_profil' => $diterima->id_profil, 'id_postingan' => $p->id_postingan, 'peran' => 'Anggota Tim']);
        $this->assertDatabaseMissing('riwayat_kolaborasi', ['id_profil' => $ditolak->id_profil]);
        $this->assertDatabaseMissing('riwayat_kolaborasi', ['id_profil' => $menunggu->id_profil]);
    }

    public function test_bukan_pembuat_ditolak(): void
    {
        $pembuat = $this->profil();
        $lain = $this->profil();
        $p = $this->buatPost($pembuat);
        $this->lamar($p, $lain, 'Diterima');

        $this->actingAs($lain->user)->post(route('postingan.selesai', $p->id_postingan))->assertForbidden();

        $this->assertSame('Aktif', $p->fresh()->status);
        $this->assertSame(0, RiwayatKolaborasi::count());
    }

    public function test_tanpa_anggota_diterima_tidak_bisa_selesai(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        $this->lamar($p, $this->profil(), 'Menunggu Konfirmasi');

        $this->actingAs($pembuat->user)
            ->post(route('postingan.selesai', $p->id_postingan))
            ->assertSessionHasErrors('selesai');

        $this->assertSame('Aktif', $p->fresh()->status);
        $this->assertSame(0, RiwayatKolaborasi::count());
    }

    public function test_tandai_selesai_dua_kali_ditolak_dan_riwayat_tidak_dobel(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        $this->lamar($p, $this->profil(), 'Diterima');

        $this->actingAs($pembuat->user)->post(route('postingan.selesai', $p->id_postingan));
        $this->actingAs($pembuat->user)
            ->post(route('postingan.selesai', $p->id_postingan))
            ->assertSessionHasErrors('selesai');

        $this->assertSame(2, RiwayatKolaborasi::count());
    }

    public function test_postingan_bukan_cari_tim_dan_tamu(): void
    {
        $pembuat = $this->profil();
        $biasa = $this->buatPost($pembuat, 'Biasa');

        $this->actingAs($pembuat->user)->post(route('postingan.selesai', $biasa->id_postingan))->assertNotFound();

        auth()->logout();
        $this->post(route('postingan.selesai', $biasa->id_postingan))->assertRedirect('/login');
    }
}
