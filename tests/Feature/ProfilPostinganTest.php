<?php

namespace Tests\Feature;

use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilPostinganTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function buatUser(string $email): User
    {
        $user = User::create([
            'name' => 'Mhs '.$email, 'tanggal_lahir' => '2004-01-01', 'email' => $email,
            'no_telepon' => '0800', 'password' => 'rahasia123',
        ]);
        ProfilMahasiswa::create(['user_id' => $user->id, 'jurusan' => 'SI', 'angkatan' => '2024']);

        return $user;
    }

    public function test_postingan_pembuat_tampil_di_profilnya(): void
    {
        $pembuat = $this->buatUser('a@student.ub.ac.id');
        $pelihat = $this->buatUser('b@student.ub.ac.id');

        Postingan::create(['id_pembuat' => $pembuat->profil->id_profil, 'kategori' => 'Biasa', 'deskripsi' => 'Halo dari profil', 'status' => 'Aktif']);
        Postingan::create(['id_pembuat' => $pelihat->profil->id_profil, 'kategori' => 'Biasa', 'deskripsi' => 'Punya orang lain', 'status' => 'Aktif']);

        $this->actingAs($pelihat)->get(route('profil.lihat', $pembuat->profil))
            ->assertOk()
            ->assertSee('Halo dari profil')
            ->assertDontSee('Punya orang lain');
    }

    public function test_cari_tim_bisa_disertai_foto(): void
    {
        Storage::fake('public');
        $user = $this->buatUser('c@student.ub.ac.id');

        $this->actingAs($user)->post('/postingan', [
            'nama_lomba' => 'Techpreneur 2026',
            'waktu_perlombaan' => '2026-12-01',
            'jumlah_anggota' => 3,
            'tag_skill' => 'Teamwork',
            'deskripsi' => 'Cari anggota tim',
            'foto' => UploadedFile::fake()->image('poster.jpg', 600, 400),
        ])->assertSessionHasNoErrors();

        $p = Postingan::first();
        $this->assertNotNull($p->foto);
        Storage::disk('public')->assertExists($p->foto);
    }
}
