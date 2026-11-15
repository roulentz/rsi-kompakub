<?php

namespace Tests\Feature;

use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostinganFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function mahasiswa(): User
    {
        $u = User::create(['name' => 'Pembuat', 'tanggal_lahir' => '2004-01-01', 'email' => 'pembuat@student.ub.ac.id', 'no_telepon' => '0800', 'password' => 'rahasia123']);
        ProfilMahasiswa::create(['user_id' => $u->id, 'jurusan' => 'Sistem Informasi', 'angkatan' => '2023', 'bio' => 'bio saya']);

        return $u;
    }

    public function test_dialog_make_a_post_ada_di_halaman_dengan_daftar_skill(): void
    {
        Skill::create(['nama_skill' => 'Public Speaking']);
        Skill::create(['nama_skill' => 'Figma']);

        $this->actingAs($this->mahasiswa())->get('/dashboard')
            ->assertOk()
            ->assertSee('Form Buat Postingan Cari Tim')
            ->assertSee(route('postingan.store'), false)
            ->assertSee('Public Speaking')
            ->assertSee('Figma')
            ->assertSee('Skill Needed');
    }

    public function test_dialog_tertutup_dan_kosong_saat_belum_ada_kegagalan(): void
    {
        $this->actingAs($this->mahasiswa())->get('/dashboard')
            ->assertDontSee('Gagal: Field berikut');
    }

    public function test_gagal_validasi_membuka_lagi_dialog_dengan_peringatan_dan_isian_lama(): void
    {
        $user = $this->mahasiswa();

        $this->actingAs($user)->from('/dashboard')
            ->post('/postingan', ['_form' => 'postingan', 'nama_lomba' => 'Gemastik'])
            ->assertRedirect('/dashboard');

        $this->actingAs($user)->get('/dashboard')
            ->assertSee('Gagal: Field berikut belum diisi - Tanggal, Jumlah anggota, Skill Needed, Katakan sesuatu')
            ->assertSee('value="Gemastik"', false);

        $this->assertSame(0, Postingan::count());
    }
}
