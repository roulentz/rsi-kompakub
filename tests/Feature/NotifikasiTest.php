<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Notifikasi;
use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    private int $n = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function profil(string $nama = null): ProfilMahasiswa
    {
        $this->n++;
        $u = User::create(['name' => $nama ?? "U{$this->n}", 'tanggal_lahir' => '2004-01-01', 'email' => "u{$this->n}@student.ub.ac.id", 'no_telepon' => '0800', 'password' => 'rahasia123']);
        return ProfilMahasiswa::create(['user_id' => $u->id, 'jurusan' => 'Sistem Informasi', 'angkatan' => '2023']);
    }

    private function buatPost(ProfilMahasiswa $pembuat, string $lomba = 'Techpreneur 2026', int $slot = 3, string $kategori = 'Cari Tim'): Postingan
    {
        return Postingan::create(['id_pembuat' => $pembuat->id_profil, 'kategori' => $kategori, 'nama_lomba' => $lomba, 'waktu_perlombaan' => '2026-10-10', 'tag_skill' => 'Research', 'jumlah_anggota' => $slot, 'deskripsi' => 'd']);
    }

    public function test_halaman_memuat_notifikasi_masuk_dan_terkirim(): void
    {
        $saya = $this->profil('Saya');
        $chelsea = $this->profil('Chelsea');
        $p1 = $this->buatPost($saya, 'Techpreneur 2026', 3);
        $this->buatPost($saya, 'Gemastik', 3);
        $this->buatPost($saya, 'Postingan biasa', 3, 'Biasa');
        foreach ([$this->profil(), $this->profil()] as $x) {
            Lamaran::create(['id_postingan' => $p1->id_postingan, 'id_pelamar' => $x->id_profil]);
        }
        Lamaran::create(['id_postingan' => $p1->id_postingan, 'id_pelamar' => $this->profil()->id_profil, 'status' => 'Diterima']);
        Notifikasi::kirim($saya->id_profil, 'Pelamar Baru', 'Terdapat pelamar baru pada postingan Anda');
        $pc = $this->buatPost($chelsea, 'Lomba Chelsea', 4);
        Lamaran::create(['id_postingan' => $pc->id_postingan, 'id_pelamar' => $saya->id_profil]);

        $r = $this->actingAs($saya->user)->get('/notifikasi')->assertOk()->assertViewIs('notifikasi.index');

        $r->assertViewHas('jumlahBelumDibaca', 1);
        $this->assertSame('Terdapat pelamar baru pada postingan Anda', $r->viewData('notifikasi')->first()->pesan);
        $masuk = $r->viewData('masuk')->keyBy('nama_lomba');
        $this->assertCount(2, $masuk);
        $this->assertSame([2, 2, 3], [$masuk['Techpreneur 2026']['pelamar_baru'], $masuk['Techpreneur 2026']['slot_terisi'], $masuk['Techpreneur 2026']['slot_total']]);
        $this->assertSame([0, 1], [$masuk['Gemastik']['pelamar_baru'], $masuk['Gemastik']['slot_terisi']]);
        $terkirim = $r->viewData('terkirim');
        $this->assertCount(1, $terkirim);
        $this->assertSame(['Chelsea', 'Lomba Chelsea', 'Menunggu Konfirmasi'], [$terkirim[0]['pembuat']['nama'], $terkirim[0]['nama_lomba'], $terkirim[0]['status']]);

        $r->assertSee('Permintaan Kolaborasi')->assertSee('Techpreneur 2026')->assertSee('2 Pelamar baru')->assertSee('Slot Anggota')
            ->assertSee('Lomba Chelsea')->assertSee('Terdapat pelamar baru pada postingan Anda')->assertSee('10 Oktober 2026');
        $r->assertDontSee('Postingan biasa');
    }

    public function test_halaman_kosong(): void
    {
        $this->actingAs($this->profil()->user)->get('/notifikasi')->assertOk()
            ->assertSee('Belum ada pelamar yang masuk')
            ->assertSee('Belum ada lamaran terkirim')
            ->assertSee('Belum ada notifikasi')
            ->assertDontSee('Tandai semua dibaca');
    }

    public function test_notifikasi_hanya_milik_sendiri_dan_terbaru_dulu(): void
    {
        $saya = $this->profil();
        $lain = $this->profil();
        Notifikasi::create(['id_penerima' => $saya->id_profil, 'tipe' => 'Diterima', 'pesan' => 'lama', 'created_at' => '2026-01-01 10:00:00']);
        Notifikasi::create(['id_penerima' => $saya->id_profil, 'tipe' => 'Ditolak', 'pesan' => 'baru', 'created_at' => '2026-02-01 10:00:00']);
        Notifikasi::kirim($lain->id_profil, 'Pelamar Baru', 'milik orang lain');
        $r = $this->actingAs($saya->user)->get('/notifikasi');
        $this->assertSame(['baru', 'lama'], $r->viewData('notifikasi')->pluck('pesan')->all());
        $r->assertDontSee('milik orang lain');
    }

    public function test_tandai_satu_dibaca(): void
    {
        $saya = $this->profil();
        $n1 = Notifikasi::kirim($saya->id_profil, 'Diterima', 'a');
        $n2 = Notifikasi::kirim($saya->id_profil, 'Ditolak', 'b');
        $this->actingAs($saya->user)->post("/notifikasi/{$n1->id_notifikasi}/dibaca")->assertRedirect();
        $this->assertTrue($n1->fresh()->status_baca);
        $this->assertFalse($n2->fresh()->status_baca);
    }

    public function test_tidak_bisa_menandai_notifikasi_orang_lain(): void
    {
        $saya = $this->profil();
        $lain = $this->profil();
        $n = Notifikasi::kirim($lain->id_profil, 'Diterima', 'a');
        $this->actingAs($saya->user)->post("/notifikasi/{$n->id_notifikasi}/dibaca")->assertForbidden();
        $this->assertFalse($n->fresh()->status_baca);
    }

    public function test_tandai_semua_dibaca_hanya_milik_sendiri(): void
    {
        $saya = $this->profil();
        $lain = $this->profil();
        Notifikasi::kirim($saya->id_profil, 'Diterima', 'a');
        Notifikasi::kirim($saya->id_profil, 'Ditolak', 'b');
        $milikLain = Notifikasi::kirim($lain->id_profil, 'Diterima', 'c');
        $this->actingAs($saya->user)->post('/notifikasi/dibaca-semua')->assertRedirect();
        $this->assertSame(0, Notifikasi::where('id_penerima', $saya->id_profil)->where('status_baca', false)->count());
        $this->assertFalse($milikLain->fresh()->status_baca);
    }

    public function test_tamu_dan_user_tanpa_profil(): void
    {
        $this->get('/notifikasi')->assertRedirect('/login');
        $this->post('/notifikasi/dibaca-semua')->assertRedirect('/login');
        $tanpa = User::create(['name' => 'T', 'tanggal_lahir' => '2004-01-01', 'email' => 't@student.ub.ac.id', 'no_telepon' => '0800', 'password' => 'rahasia123']);
        // Middleware 'onboarded' milik CV: user tanpa profil diarahkan ke wizard onboarding.
        $this->actingAs($tanpa)->get('/notifikasi')->assertRedirect(route('onboarding.form'));
        $this->actingAs($tanpa)->post('/notifikasi/dibaca-semua')->assertRedirect(route('onboarding.form'));
    }

    public function test_alur_utuh_lamar_terima_dan_lihat_notifikasi(): void
    {
        $pembuat = $this->profil('Pembuat');
        $pelamar = $this->profil('Pelamar');
        $p = $this->buatPost($pembuat);
        $this->actingAs($pelamar->user)->post("/postingan/{$p->id_postingan}/lamaran");
        $r = $this->actingAs($pembuat->user)->get('/notifikasi');
        $r->assertViewHas('jumlahBelumDibaca', 1);
        $this->assertSame(1, $r->viewData('masuk')[0]['pelamar_baru']);
        $l = Lamaran::first();
        $this->actingAs($pembuat->user)->post("/lamaran/{$l->id_lamaran}/terima");
        $r = $this->actingAs($pelamar->user)->get('/notifikasi');
        $this->assertSame('Diterima', $r->viewData('notifikasi')->first()->tipe);
        $this->assertSame('Diterima', $r->viewData('terkirim')[0]['status']);
        $m = $this->actingAs($pembuat->user)->get('/notifikasi')->viewData('masuk')[0];
        $this->assertSame([0, 2], [$m['pelamar_baru'], $m['slot_terisi']]);
    }
}
