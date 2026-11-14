<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Notifikasi;
use App\Models\Portofolio;
use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelamarTest extends TestCase
{
    use RefreshDatabase;

    private int $n = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function profil(string $nama = null, array $skill = []): ProfilMahasiswa
    {
        $this->n++;
        $u = User::create(['name' => $nama ?? "U{$this->n}", 'tanggal_lahir' => '2004-01-01', 'email' => "u{$this->n}@student.ub.ac.id", 'no_telepon' => '0800', 'password' => 'rahasia123']);
        $p = ProfilMahasiswa::create(['user_id' => $u->id, 'jurusan' => 'Sistem Informasi', 'angkatan' => '2023', 'bio' => 'bio saya']);
        foreach ($skill as $s) {
            $p->skills()->attach(Skill::firstOrCreate(['nama_skill' => $s])->id_skill);
        }
        return $p;
    }

    private function buatPost(ProfilMahasiswa $pembuat, int $slot = 3): Postingan
    {
        return Postingan::create(['id_pembuat' => $pembuat->id_profil, 'kategori' => 'Cari Tim', 'nama_lomba' => 'Techpreneur 2026', 'tag_skill' => 'Research', 'jumlah_anggota' => $slot, 'deskripsi' => 'd']);
    }

    private function lamar(Postingan $p, ProfilMahasiswa $pelamar, string $status = 'Menunggu Konfirmasi'): Lamaran
    {
        return Lamaran::create(['id_postingan' => $p->id_postingan, 'id_pelamar' => $pelamar->id_profil, 'status' => $status]);
    }

    public function test_daftar_pelamar_dan_slot(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        $maya = $this->profil('Maya', ['Research', 'Leadership']);
        $doni = $this->profil('Doni');
        $this->lamar($p, $maya);
        $this->lamar($p, $doni, 'Diterima');

        $r = $this->actingAs($pembuat->user)->get("/postingan/{$p->id_postingan}/pelamar")
            ->assertOk()->assertViewIs('pelamar.index');

        $r->assertViewHas('slotTerisi', 2);
        $daftar = $r->viewData('daftarLamaran');
        $this->assertSame(['Maya', 'Doni'], $daftar->map(fn ($l) => $l->pelamar->user->name)->all());
        $this->assertSame('Menunggu Konfirmasi', $daftar[0]->status);
        $this->assertEqualsCanonicalizing(['Research', 'Leadership'], $daftar[0]->pelamar->skills->pluck('nama_skill')->all());
        $r->assertSee('Techpreneur 2026')->assertSee('2 Pelamar')->assertSee('Slot Anggota')->assertSee('2/3')
            ->assertSee('Maya')->assertSee('Leadership')->assertSee('Lihat Profil')
            ->assertSee('Menunggu Konfirmasi')->assertSee('Diterima')
            ->assertSee(route('pelamar.show', $daftar[0]->id_lamaran), false)
            ->assertSee(route('postingan.rekomendasi', $p->id_postingan), false);
    }

    public function test_daftar_pelamar_kosong(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        $this->actingAs($pembuat->user)->get("/postingan/{$p->id_postingan}/pelamar")->assertOk()
            ->assertViewHas('slotTerisi', 1)
            ->assertSee('0 Pelamar')->assertSee('Belum ada pelamar yang masuk');
    }

    public function test_profil_lengkap_pelamar_dengan_portofolio_berurutan(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        $maya = $this->profil('Maya', ['Research']);
        Portofolio::create(['id_profil' => $maya->id_profil, 'konten' => 'Slide dua', 'urutan_halaman' => 2, 'tautan_eksternal' => null, 'ukuran_file' => 1.5]);
        Portofolio::create(['id_profil' => $maya->id_profil, 'konten' => 'Slide satu', 'urutan_halaman' => 1, 'tautan_eksternal' => 'https://x.test', 'ukuran_file' => 1.5]);
        $l = $this->lamar($p, $maya);

        $r = $this->actingAs($pembuat->user)->get("/lamaran/{$l->id_lamaran}")->assertOk()->assertViewIs('pelamar.show');

        $this->assertSame(['Slide satu', 'Slide dua'], $r->viewData('portofolio')->pluck('konten')->all());
        $r->assertSee('Maya')->assertSee('bio saya')->assertSee('Keahlian')->assertSee('Research')
            ->assertSee('Terima')->assertSee('Tolak')->assertSee('Portofolio')
            ->assertSee('Slide satu')->assertSee('x.test'); // tautan ikut dikirim ke penampil slide (JSON meng-escape garis miring)
        // Kontak pribadi tidak boleh tampil di halaman pelamar.
        $r->assertDontSee($maya->user->email)->assertDontSee('0800');
    }

    public function test_tautan_portofolio_bukan_http_tidak_ditampilkan(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        $maya = $this->profil('Maya');
        Portofolio::create(['id_profil' => $maya->id_profil, 'konten' => 'S', 'urutan_halaman' => 1, 'tautan_eksternal' => 'javascript:alert(1)', 'ukuran_file' => 1.0]);
        $l = $this->lamar($p, $maya);

        $this->actingAs($pembuat->user)->get("/lamaran/{$l->id_lamaran}")->assertOk()->assertDontSee('javascript:alert(1)', false);
    }

    public function test_profil_pelamar_yang_sudah_diputuskan_tanpa_tombol_dan_slot_penuh_menonaktifkan_terima(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat, 2);
        $a = $this->lamar($p, $this->profil('Anton'), 'Diterima');
        $b = $this->lamar($p, $this->profil('Budi'));

        $this->actingAs($pembuat->user)->get("/lamaran/{$a->id_lamaran}")->assertOk()
            ->assertDontSee(route('pelamar.terima', $a->id_lamaran), false)
            ->assertSee('tidak bisa diubah');
        $this->actingAs($pembuat->user)->get("/lamaran/{$b->id_lamaran}")->assertOk()
            ->assertSee('Slot anggota sudah penuh')->assertSee('disabled', false);
    }

    public function test_terima_mengubah_status_dan_mengirim_notifikasi(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        $maya = $this->profil('Maya');
        $l = $this->lamar($p, $maya);
        $r = $this->actingAs($pembuat->user)->post("/lamaran/{$l->id_lamaran}/terima");
        $r->assertSessionHas('status', 'Keputusan berhasil disimpan')->assertSessionHas('status_lamaran', 'Diterima');
        $this->assertSame('Diterima', $l->fresh()->status);
        $n = Notifikasi::first();
        $this->assertSame($maya->id_profil, $n->id_penerima);
        $this->assertSame('Diterima', $n->tipe);
        $this->assertSame('Lamaran Anda telah diterima', $n->pesan);
        $this->assertSame(2, $p->slotTerisi());
    }

    public function test_tolak_mengubah_status_dan_mengirim_notifikasi(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        $maya = $this->profil('Maya');
        $l = $this->lamar($p, $maya);
        $this->actingAs($pembuat->user)->post("/lamaran/{$l->id_lamaran}/tolak")->assertSessionHas('status_lamaran', 'Ditolak');
        $this->assertSame('Ditolak', $l->fresh()->status);
        $n = Notifikasi::first();
        $this->assertSame(['Ditolak', 'Lamaran Anda belum dapat diterima', $maya->id_profil], [$n->tipe, $n->pesan, $n->id_penerima]);
        $this->assertSame(1, $p->slotTerisi());
    }

    public function test_keputusan_tidak_bisa_diulang(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        $l = $this->lamar($p, $this->profil());
        $this->actingAs($pembuat->user)->post("/lamaran/{$l->id_lamaran}/terima");
        $this->actingAs($pembuat->user)->post("/lamaran/{$l->id_lamaran}/tolak")->assertSessionHasErrors(['keputusan' => 'Lamaran ini sudah diputuskan']);
        $this->assertSame('Diterima', $l->fresh()->status);
        $this->assertSame(1, Notifikasi::count());
    }

    public function test_terima_melebihi_slot_ditolak_tapi_tolak_tetap_bisa(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat, 2);
        $a = $this->lamar($p, $this->profil());
        $b = $this->lamar($p, $this->profil());
        $this->actingAs($pembuat->user)->post("/lamaran/{$a->id_lamaran}/terima")->assertSessionHasNoErrors();
        $this->actingAs($pembuat->user)->post("/lamaran/{$b->id_lamaran}/terima")->assertSessionHasErrors(['keputusan' => 'Slot anggota sudah penuh']);
        $this->assertSame('Menunggu Konfirmasi', $b->fresh()->status);
        $this->assertSame(1, Notifikasi::count());
        $this->actingAs($pembuat->user)->post("/lamaran/{$b->id_lamaran}/tolak")->assertSessionHasNoErrors();
        $this->assertSame('Ditolak', $b->fresh()->status);
    }

    public function test_hanya_pembuat_yang_boleh_mengelola(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        $pelamar = $this->profil();
        $lain = $this->profil();
        $l = $this->lamar($p, $pelamar);
        foreach ([$lain, $pelamar] as $bukan) {
            $this->actingAs($bukan->user)->get("/postingan/{$p->id_postingan}/pelamar")->assertForbidden();
            $this->actingAs($bukan->user)->get("/lamaran/{$l->id_lamaran}")->assertForbidden();
            $this->actingAs($bukan->user)->post("/lamaran/{$l->id_lamaran}/terima")->assertForbidden();
            $this->actingAs($bukan->user)->post("/lamaran/{$l->id_lamaran}/tolak")->assertForbidden();
        }
        $this->assertSame('Menunggu Konfirmasi', $l->fresh()->status);
        $this->assertSame(0, Notifikasi::count());
    }

    public function test_tamu_dan_data_tidak_ada(): void
    {
        $pembuat = $this->profil();
        $p = $this->buatPost($pembuat);
        $l = $this->lamar($p, $this->profil());
        $this->post("/lamaran/{$l->id_lamaran}/terima")->assertRedirect('/login');
        $this->actingAs($pembuat->user)->post('/lamaran/9999/terima')->assertNotFound();
        $this->actingAs($pembuat->user)->get('/postingan/9999/pelamar')->assertNotFound();
    }
}
