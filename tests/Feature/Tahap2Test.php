<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Notifikasi;
use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\Rekomendasi;
use App\Models\RiwayatKolaborasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Tahap 2: pencapaian saat Tandai Selesai, kunci tanggal lomba, edit peran, rekomendasi per proyek, notifikasi.
class Tahap2Test extends TestCase
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

    private function proyek(ProfilMahasiswa $pembuat, ProfilMahasiswa $anggota, string $tanggal = '2026-01-10'): Postingan
    {
        $p = Postingan::create(['id_pembuat' => $pembuat->id_profil, 'kategori' => 'Cari Tim', 'nama_lomba' => 'Lomba Alfa', 'waktu_perlombaan' => $tanggal, 'tag_skill' => 'Figma', 'jumlah_anggota' => 3, 'deskripsi' => 'd']);
        Lamaran::create(['id_postingan' => $p->id_postingan, 'id_pelamar' => $anggota->id_profil, 'status' => 'Diterima']);

        return $p;
    }

    public function test_tandai_selesai_menyimpan_pencapaian_peran_notifikasi_dan_tanggal_lomba(): void
    {
        $pembuat = $this->profil();
        $anggota = $this->profil();
        $p = $this->proyek($pembuat, $anggota, '2026-01-10');

        $this->actingAs($pembuat->user)->post(route('postingan.selesai', $p->id_postingan), [
            'pencapaian' => 'Juara 2', 'nama_proyek' => 'Platform Kost', 'peran_saya' => 'Lead Designer',
        ])->assertSessionHas('status');

        $p->refresh();
        $this->assertSame('Selesai', $p->status);
        $this->assertSame('Juara 2', $p->pencapaian);
        $this->assertSame('Platform Kost', $p->nama_proyek);

        // SQLite menyimpan kolom tanggal sebagai "2026-01-10 00:00:00", jadi bandingkan lewat model (cast date).
        $riwayatPembuat = RiwayatKolaborasi::where('id_profil', $pembuat->id_profil)->first();
        $riwayatAnggota = RiwayatKolaborasi::where('id_profil', $anggota->id_profil)->first();
        $this->assertSame('Lead Designer', $riwayatPembuat->peran);
        $this->assertSame('Anggota Tim', $riwayatAnggota->peran);
        $this->assertSame('2026-01-10', $riwayatPembuat->tanggal_selesai->toDateString());
        $this->assertSame('2026-01-10', $riwayatAnggota->tanggal_selesai->toDateString());

        // Anggota diberi tahu, pembuat tidak.
        $this->assertDatabaseHas('notifikasi', ['id_penerima' => $anggota->id_profil, 'tipe' => 'Proyek Selesai']);
        $this->assertDatabaseMissing('notifikasi', ['id_penerima' => $pembuat->id_profil, 'tipe' => 'Proyek Selesai']);
    }

    public function test_pencapaian_wajib_dan_harus_dari_daftar(): void
    {
        $pembuat = $this->profil();
        $p = $this->proyek($pembuat, $this->profil());

        $this->actingAs($pembuat->user)->post(route('postingan.selesai', $p->id_postingan), [])->assertSessionHasErrors('pencapaian');
        $this->actingAs($pembuat->user)->post(route('postingan.selesai', $p->id_postingan), ['pencapaian' => 'Juara Umum'])->assertSessionHasErrors('pencapaian');

        $this->assertSame('Aktif', $p->fresh()->status);
    }

    public function test_belum_bisa_selesai_sebelum_tanggal_lomba(): void
    {
        $pembuat = $this->profil();
        $p = $this->proyek($pembuat, $this->profil(), now()->addDays(10)->toDateString());

        $this->actingAs($pembuat->user)->post(route('postingan.selesai', $p->id_postingan), ['pencapaian' => 'Peserta'])
            ->assertSessionHasErrors('selesai');

        $this->assertSame('Aktif', $p->fresh()->status);
        $this->assertSame(0, RiwayatKolaborasi::count());
    }

    public function test_hanya_pembuat_yang_bisa_mengubah_detail_setelah_selesai(): void
    {
        $pembuat = $this->profil();
        $anggota = $this->profil();
        $p = $this->proyek($pembuat, $anggota);

        // Belum selesai: tidak ada.
        $this->actingAs($pembuat->user)->patch(route('postingan.detail-proyek', $p->id_postingan), ['pencapaian' => 'Juara 1'])->assertNotFound();

        $this->actingAs($pembuat->user)->post(route('postingan.selesai', $p->id_postingan), ['pencapaian' => 'Finalis']);
        $this->actingAs($anggota->user)->patch(route('postingan.detail-proyek', $p->id_postingan), ['pencapaian' => 'Juara 1'])->assertForbidden();
        $this->actingAs($pembuat->user)->patch(route('postingan.detail-proyek', $p->id_postingan), ['pencapaian' => 'Juara 3', 'nama_proyek' => 'Baru'])->assertSessionHas('status');

        $this->assertSame('Juara 3', $p->fresh()->pencapaian);
    }

    public function test_anggota_mengubah_peran_sendiri_saja(): void
    {
        $pembuat = $this->profil();
        $anggota = $this->profil();
        $p = $this->proyek($pembuat, $anggota);
        RiwayatKolaborasi::catatProyekSelesai($p->id_postingan, 'Lomba Alfa', $pembuat->id_profil, [$anggota->id_profil]);
        $milikAnggota = RiwayatKolaborasi::where('id_profil', $anggota->id_profil)->first();

        $this->actingAs($pembuat->user)->patch(route('riwayat.update', $milikAnggota), ['peran' => 'Hack'])->assertForbidden();
        $this->actingAs($anggota->user)->patch(route('riwayat.update', $milikAnggota), ['peran' => ''])->assertSessionHasErrors('peran');
        $this->actingAs($anggota->user)->patch(route('riwayat.update', $milikAnggota), ['peran' => 'UI Researcher'])->assertSessionHas('status');

        $this->assertSame('UI Researcher', $milikAnggota->fresh()->peran);

        // Memanggil ulang pencatatan tidak menimpa peran yang sudah diedit.
        RiwayatKolaborasi::catatProyekSelesai($p->id_postingan, 'Lomba Alfa', $pembuat->id_profil, [$anggota->id_profil]);
        $this->assertSame('UI Researcher', $milikAnggota->fresh()->peran);
    }

    public function test_rekomendasi_terhubung_proyek_satu_kali_dan_memberi_notifikasi(): void
    {
        $pembuat = $this->profil('Pembuat');
        $anggota = $this->profil('Anggota');
        $p = $this->proyek($pembuat, $anggota);
        RiwayatKolaborasi::catatProyekSelesai($p->id_postingan, 'Lomba Alfa', $pembuat->id_profil, [$anggota->id_profil]);

        $this->actingAs($anggota->user)->post(route('rekomendasi.store', $pembuat), ['isi_rekomendasi' => 'Sangat rapi dan tepat waktu.'])
            ->assertSessionHas('status');

        $this->assertDatabaseHas('rekomendasi', ['id_profil_pemberi' => $anggota->id_profil, 'id_profil_penerima' => $pembuat->id_profil, 'id_postingan' => $p->id_postingan]);
        $this->assertDatabaseHas('notifikasi', ['id_penerima' => $pembuat->id_profil, 'tipe' => 'Rekomendasi Baru']);

        // Kedua kali untuk proyek yang sama ditolak.
        $this->actingAs($anggota->user)->post(route('rekomendasi.store', $pembuat), ['isi_rekomendasi' => 'Lagi'])
            ->assertSessionHasErrors('isi_rekomendasi');
        $this->assertSame(1, Rekomendasi::count());

        $this->actingAs($pembuat->user)->get(route('profil.lihat', $pembuat))
            ->assertOk()
            ->assertSee('Partner di Lomba Alfa')
            ->assertSee('Sangat rapi dan tepat waktu.');
    }

    public function test_riwayat_menampilkan_pencapaian_dan_nama_proyek(): void
    {
        $pembuat = $this->profil();
        $anggota = $this->profil();
        $p = $this->proyek($pembuat, $anggota);
        $this->actingAs($pembuat->user)->post(route('postingan.selesai', $p->id_postingan), ['pencapaian' => 'Juara 1', 'nama_proyek' => 'Aplikasi Sampah Organik']);

        $this->actingAs($anggota->user)->get(route('profil.lihat', $anggota))
            ->assertOk()
            ->assertSee('Juara 1 Lomba Alfa')
            ->assertSee('Aplikasi Sampah Organik');
    }
}
