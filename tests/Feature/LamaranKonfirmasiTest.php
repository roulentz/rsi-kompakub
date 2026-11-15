<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LamaranKonfirmasiTest extends TestCase
{
    use RefreshDatabase;

    private int $n = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function profil(bool $terverifikasi = true): ProfilMahasiswa
    {
        $this->n++;
        $u = User::create(['name' => "Mhs{$this->n}", 'tanggal_lahir' => '2004-01-01', 'email' => "k{$this->n}@student.ub.ac.id", 'no_telepon' => '0800', 'password' => 'rahasia123']);
        if ($terverifikasi) {
            $u->forceFill(['email_verified_at' => now()])->save();
        }

        return ProfilMahasiswa::create(['user_id' => $u->id, 'jurusan' => 'Sistem Informasi', 'angkatan' => '2023', 'bio' => 'bio saya']);
    }

    private function buatPost(ProfilMahasiswa $pembuat): Postingan
    {
        return Postingan::create(['id_pembuat' => $pembuat->id_profil, 'kategori' => 'Cari Tim', 'nama_lomba' => 'Techpreneur 2026', 'waktu_perlombaan' => '2026-12-01', 'tag_skill' => 'Research,Figma', 'jumlah_anggota' => 4, 'deskripsi' => 'd']);
    }

    public function test_detail_postingan_menampilkan_dialog_konfirmasi_dengan_data_pelamar(): void
    {
        $p = $this->buatPost($this->profil());
        $pelamar = $this->profil();

        $this->actingAs($pelamar->user)->get(route('postingan.show', $p->id_postingan))
            ->assertOk()
            ->assertSee('Konfirmasi Lamaran')
            ->assertSee('Techpreneur 2026')
            ->assertSee('Research, Figma')
            ->assertSee($pelamar->user->name.' (Sistem Informasi', false)
            ->assertSee('&#039;23', false)
            ->assertSee('Mahasiswa Aktif UB')
            ->assertSee('Ya, Ajukan')
            ->assertSee(route('lamaran.store', $p->id_postingan), false);
    }

    public function test_beranda_memakai_dialog_bukan_kirim_langsung(): void
    {
        $p = $this->buatPost($this->profil());
        $pelamar = $this->profil();

        $this->actingAs($pelamar->user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('konfirmasi-lamaran-'.$p->id_postingan, false)
            ->assertSee('Ya, Ajukan');
    }

    public function test_pembuat_sendiri_tidak_mendapat_dialog(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);

        $this->actingAs($pembuat->user)->get(route('dashboard'))->assertOk()->assertDontSee('Konfirmasi Lamaran');
        $this->actingAs($pembuat->user)->get(route('postingan.show', $p->id_postingan))->assertOk()->assertDontSee('Konfirmasi Lamaran');
    }

    public function test_yang_sudah_melamar_atau_proyek_selesai_tidak_mendapat_dialog(): void
    {
        $p = $this->buatPost($this->profil());
        $pelamar = $this->profil();
        Lamaran::create(['id_postingan' => $p->id_postingan, 'id_pelamar' => $pelamar->id_profil, 'status' => 'Menunggu Konfirmasi']);

        $this->actingAs($pelamar->user)->get(route('postingan.show', $p->id_postingan))->assertDontSee('Konfirmasi Lamaran');

        $p->update(['status' => 'Selesai']);
        $lain = $this->profil();
        $this->actingAs($lain->user)->get(route('postingan.show', $p->id_postingan))->assertDontSee('Konfirmasi Lamaran');
    }

    public function test_status_verifikasi_belum_terverifikasi(): void
    {
        $p = $this->buatPost($this->profil());
        $pelamar = $this->profil(false);

        $this->actingAs($pelamar->user)->get(route('postingan.show', $p->id_postingan))
            ->assertSee('Belum terverifikasi')
            ->assertDontSee('Mahasiswa Aktif UB');
    }
}
