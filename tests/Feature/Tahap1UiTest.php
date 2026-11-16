<?php

namespace Tests\Feature;

use App\Models\PreferensiUser;
use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\Skill;
use App\Models\User;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Tahap1UiTest extends TestCase
{
    use RefreshDatabase;

    private int $n = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function profil(string $nama): ProfilMahasiswa
    {
        $this->n++;
        $u = User::create(['name' => $nama, 'tanggal_lahir' => '2004-01-01', 'email' => "u{$this->n}@student.ub.ac.id", 'no_telepon' => '0800', 'password' => 'rahasia123']);

        return ProfilMahasiswa::create(['user_id' => $u->id, 'jurusan' => 'Sistem Informasi', 'angkatan' => '2023']);
    }

    public function test_tab_umum_hanya_menampilkan_postingan_biasa_dan_tersimpan_sebagai_preferensi(): void
    {
        $saya = $this->profil('Saya');
        Postingan::create(['id_pembuat' => $saya->id_profil, 'kategori' => 'Biasa', 'deskripsi' => 'Pamer prestasi juara satu']);
        Postingan::create(['id_pembuat' => $saya->id_profil, 'kategori' => 'Info Lomba', 'nama_lomba' => 'Lomba Poster', 'waktu_perlombaan' => '2026-12-01', 'deskripsi' => 'Info lomba poster']);

        $this->actingAs($saya->user)->get('/dashboard?kategori=Umum')
            ->assertOk()
            ->assertSee('Beranda')
            ->assertSee('Pamer prestasi juara satu')
            ->assertDontSee('Info lomba poster');

        $this->assertSame('Umum', PreferensiUser::where('id_profil', $saya->id_profil)->value('kategori_terakhir'));
    }

    public function test_profil_orang_lain_memakai_user_id_untuk_tombol_pesan_dan_punya_tab(): void
    {
        // Pengguna tanpa profil dibuat dulu supaya user_id dan id_profil berbeda.
        User::create(['name' => 'Admin', 'tanggal_lahir' => '1990-01-01', 'email' => 'x@student.ub.ac.id', 'no_telepon' => '0800', 'password' => 'rahasia123']);
        User::create(['name' => 'Admin2', 'tanggal_lahir' => '1990-01-01', 'email' => 'y@student.ub.ac.id', 'no_telepon' => '0800', 'password' => 'rahasia123']);
        $saya = $this->profil('Saya');
        $lain = $this->profil('Maya');
        $this->assertNotSame($lain->user_id, $lain->id_profil);

        $this->actingAs($saya->user)->get(route('profil.lihat', $lain))
            ->assertOk()
            ->assertSee(route('dm.mulai', $lain->user_id), false)
            ->assertSee('Riwayat Kolaborasi')
            ->assertSee('Rekomendasi')
            ->assertSee('Portofolio');
    }

    public function test_daftar_skill_lengkap_tanpa_koma_dan_tampil_di_formulir(): void
    {
        $this->seed(SkillSeeder::class);

        $this->assertGreaterThan(300, Skill::count());
        $this->assertSame(0, Skill::where('nama_skill', 'like', '%,%')->count());
        foreach (Skill::POPULER as $nama) {
            $this->assertTrue(Skill::where('nama_skill', $nama)->exists(), "Skill populer tidak ada: {$nama}");
        }

        $saya = $this->profil('Saya');
        $this->actingAs($saya->user)->get(route('profil.edit'))
            ->assertOk()
            ->assertSee('Cari keahlian')
            ->assertSee('Adobe Photoshop');
    }
}
