<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Notifikasi;
use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LamaranStoreTest extends TestCase
{
    use RefreshDatabase;

    private int $n = 0;

    private function profil(): ProfilMahasiswa
    {
        $this->n++;
        $u = User::create(['name' => "U{$this->n}", 'tanggal_lahir' => '2004-01-01', 'email' => "u{$this->n}@student.ub.ac.id", 'no_telepon' => '0800', 'password' => 'rahasia123']);
        return ProfilMahasiswa::create(['user_id' => $u->id, 'jurusan' => 'SI', 'angkatan' => '2023']);
    }

    private function buatPost(ProfilMahasiswa $pembuat, array $ubah = []): Postingan
    {
        return Postingan::create(array_merge(['id_pembuat' => $pembuat->id_profil, 'kategori' => 'Cari Tim', 'nama_lomba' => 'X', 'tag_skill' => 'Research', 'jumlah_anggota' => 3, 'deskripsi' => 'd'], $ubah));
    }

    public function test_lamaran_valid_menyimpan_lamaran_dan_notifikasi(): void
    {
        $pembuat = $this->profil();
        $pelamar = $this->profil();
        $p = $this->buatPost($pembuat);
        $r = $this->actingAs($pelamar->user)->from('/beranda')->post("/postingan/{$p->id_postingan}/lamaran");
        $r->assertRedirect('/beranda');
        $r->assertSessionHas('status', 'Lamaran berhasil diajukan');
        $r->assertSessionHas('status_lamaran', 'Menunggu Konfirmasi');
        $l = Lamaran::first();
        $this->assertSame('Menunggu Konfirmasi', $l->status);
        $this->assertSame($pelamar->id_profil, $l->id_pelamar);
        $this->assertSame($p->id_postingan, $l->id_postingan);
        $this->assertNotNull($l->tanggal_lamar);
        $this->assertSame(1, Notifikasi::count());
        $n = Notifikasi::first();
        $this->assertSame($pembuat->id_profil, $n->id_penerima);
        $this->assertSame('Pelamar Baru', $n->tipe);
        $this->assertSame('Terdapat pelamar baru pada postingan Anda', $n->pesan);
        $this->assertFalse($n->fresh()->status_baca);
        $this->assertNotNull($n->fresh()->created_at);
    }

    public function test_lamaran_ganda_ditolak_tanpa_notifikasi_kedua(): void
    {
        $pelamar = $this->profil();
        $p = $this->buatPost($this->profil());
        $this->actingAs($pelamar->user)->post("/postingan/{$p->id_postingan}/lamaran");
        $r = $this->actingAs($pelamar->user)->post("/postingan/{$p->id_postingan}/lamaran");
        $r->assertSessionHasErrors(['lamaran' => 'Anda sudah melamar ke postingan ini']);
        $this->assertSame(1, Lamaran::count());
        $this->assertSame(1, Notifikasi::count());
    }

    public function test_pembuat_tidak_bisa_melamar_postingan_sendiri(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        $this->actingAs($pembuat->user)->post("/postingan/{$p->id_postingan}/lamaran")->assertForbidden();
        $this->assertSame(0, Lamaran::count());
        $this->assertSame(0, Notifikasi::count());
    }

    public function test_postingan_selesai_ditolak(): void
    {
        $p = $this->buatPost($this->profil(), ['status' => 'Selesai']);
        $r = $this->actingAs($this->profil()->user)->post("/postingan/{$p->id_postingan}/lamaran");
        $r->assertSessionHasErrors('lamaran');
        $this->assertSame(0, Lamaran::count());
        $this->assertSame(0, Notifikasi::count());
    }

    public function test_postingan_bukan_cari_tim_atau_tidak_ada(): void
    {
        $biasa = $this->buatPost($this->profil(), ['kategori' => 'Biasa']);
        $u = $this->profil()->user;
        $this->actingAs($u)->post("/postingan/{$biasa->id_postingan}/lamaran")->assertNotFound();
        $this->actingAs($u)->post('/postingan/9999/lamaran')->assertNotFound();
        $this->assertSame(0, Lamaran::count());
    }

    public function test_tamu_dan_user_tanpa_profil(): void
    {
        $p = $this->buatPost($this->profil());
        $this->post("/postingan/{$p->id_postingan}/lamaran")->assertRedirect('/login');
        $tanpa = User::create(['name' => 'T', 'tanggal_lahir' => '2004-01-01', 'email' => 't@student.ub.ac.id', 'no_telepon' => '0800', 'password' => 'rahasia123']);
        // Middleware 'onboarded' milik CV: user tanpa profil diarahkan ke wizard onboarding.
        $this->actingAs($tanpa)->post("/postingan/{$p->id_postingan}/lamaran")->assertRedirect(route('onboarding.form'));
        $this->assertSame(0, Lamaran::count());
    }

    public function test_route_kontrak_untuk_mz(): void
    {
        $this->assertSame(url('/postingan/5/lamaran'), route('lamaran.store', 5));
        $this->assertSame(url('/postingan/5/lamaran'), route('lamaran.store', ['postingan_id' => 5]));
    }
}
