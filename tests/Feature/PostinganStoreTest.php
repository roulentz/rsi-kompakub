<?php

namespace Tests\Feature;

use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostinganStoreTest extends TestCase
{
    use RefreshDatabase;

    private function buatUser(bool $denganProfil = true, string $email = 'sh@student.ub.ac.id'): User
    {
        $user = User::create([
            'name' => 'Shilvia', 'tanggal_lahir' => '2004-01-01', 'email' => $email,
            'no_telepon' => '0800', 'password' => 'rahasia123',
        ]);
        if ($denganProfil) {
            ProfilMahasiswa::create(['user_id' => $user->id, 'jurusan' => 'SI', 'angkatan' => '2022']);
        }
        return $user;
    }

    private function data(array $ubah = []): array
    {
        return array_merge([
            'nama_lomba' => 'Techpreneur 2026',
            'waktu_perlombaan' => '2026-10-10',
            'jumlah_anggota' => 3,
            'tag_skill' => 'Public Speaking',
            'deskripsi' => 'Halo warga UB, cari anggota tim.',
        ], $ubah);
    }

    public function test_simpan_postingan_valid(): void
    {
        $user = $this->buatUser();
        $r = $this->actingAs($user)->post('/postingan', $this->data(['jenisLomba' => 'x', 'jumlahAnggotaTerisi' => 1]));
        $p = Postingan::first();
        $r->assertRedirect(route('postingan.show', $p->id_postingan));
        $r->assertSessionHas('status', 'Postingan berhasil diterbitkan');
        $this->assertSame('Cari Tim', $p->kategori);
        $this->assertSame('Aktif', $p->status);
        $this->assertSame($user->profil->id_profil, $p->id_pembuat);
        $this->assertSame(3, $p->jumlah_anggota);
        $r->assertSessionHas('id_postingan', $p->id_postingan);
    }

    public function test_kategori_dan_pembuat_tidak_bisa_dipalsukan(): void
    {
        $user = $this->buatUser();
        $lain = $this->buatUser(true, 'lain@student.ub.ac.id');
        $this->actingAs($user)->post('/postingan', $this->data(['kategori' => 'Biasa', 'status' => 'Selesai', 'id_pembuat' => $lain->profil->id_profil]));
        $p = Postingan::first();
        $this->assertSame('Cari Tim', $p->kategori);
        $this->assertSame('Aktif', $p->status);
        $this->assertSame($user->profil->id_profil, $p->id_pembuat);
    }

    public function test_semua_field_kosong_memberi_pesan_peringatan(): void
    {
        $r = $this->actingAs($this->buatUser())->post('/postingan', []);
        $r->assertSessionHasErrors(['nama_lomba', 'waktu_perlombaan', 'jumlah_anggota', 'tag_skill', 'deskripsi']);
        $r->assertSessionHas('pesan_peringatan', 'Gagal: Field berikut belum diisi - Nama Lomba, Tanggal, Jumlah anggota, Skill Needed, Katakan sesuatu');
        $this->assertSame(0, Postingan::count());
    }

    public function test_sebagian_field_kosong(): void
    {
        $r = $this->actingAs($this->buatUser())->post('/postingan', $this->data(['nama_lomba' => '', 'deskripsi' => '']));
        $r->assertSessionHas('pesan_peringatan', 'Gagal: Field berikut belum diisi - Nama Lomba, Katakan sesuatu');
        $this->assertSame(0, Postingan::count());
    }

    public function test_batas_jumlah_anggota(): void
    {
        $u = $this->buatUser();
        foreach ([0, 11] as $n) {
            $r = $this->actingAs($u)->post('/postingan', $this->data(['jumlah_anggota' => $n]));
            $r->assertSessionHasErrors('jumlah_anggota');
            $r->assertSessionMissing('pesan_peringatan');
        }
        $this->assertSame(0, Postingan::count());
        foreach ([1, 10] as $n) {
            $this->actingAs($u)->post('/postingan', $this->data(['jumlah_anggota' => $n]))->assertSessionHasNoErrors();
        }
        $this->assertSame(2, Postingan::count());
    }

    public function test_batas_deskripsi_500(): void
    {
        $u = $this->buatUser();
        $this->actingAs($u)->post('/postingan', $this->data(['deskripsi' => str_repeat('a', 501)]))->assertSessionHasErrors('deskripsi');
        $this->actingAs($u)->post('/postingan', $this->data(['deskripsi' => str_repeat('a', 500)]))->assertSessionHasNoErrors();
        $this->assertSame(1, Postingan::count());
    }

    public function test_nama_lomba_maks_100(): void
    {
        $this->actingAs($this->buatUser())->post('/postingan', $this->data(['nama_lomba' => str_repeat('a', 101)]))->assertSessionHasErrors('nama_lomba');
    }

    public function test_tanggal_tidak_valid_ditolak(): void
    {
        $this->actingAs($this->buatUser())->post('/postingan', $this->data(['waktu_perlombaan' => 'bukan tanggal']))->assertSessionHasErrors('waktu_perlombaan');
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->post('/postingan', $this->data())->assertRedirect('/login');
        $this->assertSame(0, Postingan::count());
    }

    public function test_user_tanpa_profil_diarahkan_ke_onboarding(): void
    {
        // Middleware 'onboarded' milik CV: user tanpa profil diarahkan ke wizard onboarding.
        $this->actingAs($this->buatUser(false))->post('/postingan', $this->data())->assertRedirect(route('onboarding.form'));
        $this->assertSame(0, Postingan::count());
    }
}
