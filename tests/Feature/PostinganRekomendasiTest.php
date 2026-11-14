<?php

namespace Tests\Feature;

use App\Models\Postingan;
use App\Models\ProfilMahasiswa;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostinganRekomendasiTest extends TestCase
{
    use RefreshDatabase;

    private int $n = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function profil(string $nama, array $skill = []): ProfilMahasiswa
    {
        $this->n++;
        $u = User::create(['name' => $nama, 'tanggal_lahir' => '2004-01-01', 'email' => "u{$this->n}@student.ub.ac.id", 'no_telepon' => '0800', 'password' => 'rahasia123']);
        $p = ProfilMahasiswa::create(['user_id' => $u->id, 'jurusan' => 'Sistem Informasi', 'angkatan' => '2023']);
        foreach ($skill as $s) {
            $p->skills()->attach(Skill::firstOrCreate(['nama_skill' => $s])->id_skill);
        }
        return $p;
    }

    private function buatPost(ProfilMahasiswa $pembuat, string $tag, string $kategori = 'Cari Tim'): Postingan
    {
        return Postingan::create(['id_pembuat' => $pembuat->id_profil, 'kategori' => $kategori, 'nama_lomba' => 'X', 'tag_skill' => $tag, 'jumlah_anggota' => 3, 'deskripsi' => 'd']);
    }

    public function test_kandidat_cocok_urut_berdasarkan_jumlah_skill_cocok(): void
    {
        $pembuat = $this->profil('Pembuat', ['Public Speaking']);
        $a = $this->profil('A satu cocok', ['Public Speaking']);
        $b = $this->profil('B dua cocok', ['Public Speaking', 'Communication', 'Leadership']);
        $this->profil('C tidak cocok', ['Photography']);
        $this->profil('D tanpa skill');
        $hasil = $this->buatPost($pembuat, 'Public Speaking, Communication')->rekomendasiKandidat();
        $this->assertSame([$b->id_profil, $a->id_profil], $hasil->pluck('id_profil')->all());
        $this->assertSame([2, 1], $hasil->pluck('skill_cocok')->all());
    }

    public function test_pembuat_tidak_direkomendasikan_ke_dirinya_sendiri(): void
    {
        $pembuat = $this->profil('Pembuat', ['Public Speaking']);
        $this->assertCount(0, $this->buatPost($pembuat, 'Public Speaking')->rekomendasiKandidat());
    }

    public function test_maksimal_10_kandidat(): void
    {
        $pembuat = $this->profil('Pembuat');
        for ($i = 0; $i < 12; $i++) {
            $this->profil("K$i", ['Research']);
        }
        $this->assertCount(10, $this->buatPost($pembuat, 'Research')->rekomendasiKandidat());
    }

    public function test_tag_skill_kosong_atau_tidak_dikenal(): void
    {
        $pembuat = $this->profil('Pembuat');
        $this->profil('K', ['Research']);
        $this->assertCount(0, $this->buatPost($pembuat, ' , ')->rekomendasiKandidat());
        $this->assertCount(0, $this->buatPost($pembuat, 'Business Model')->rekomendasiKandidat());
    }

    public function test_halaman_untuk_pembuat(): void
    {
        $pembuat = $this->profil('Pembuat');
        $k = $this->profil('Dian', ['Public Speaking', 'Research']);
        $p = $this->buatPost($pembuat, 'Public Speaking');

        $r = $this->actingAs($pembuat->user)->get("/postingan/{$p->id_postingan}/rekomendasi")
            ->assertOk()->assertViewIs('postingan.rekomendasi');

        $kandidat = $r->viewData('kandidat');
        $this->assertSame([$k->id_profil], $kandidat->pluck('id_profil')->all());
        $this->assertSame(1, $kandidat[0]->skill_cocok);
        $r->assertSee('Rekomendasi Kandidat')->assertSee('Dian')->assertSee('Sistem Informasi - 2023')
            ->assertSee('Public Speaking')->assertSee('Research')->assertSee('1 skill cocok')
            ->assertSee(route('profil.lihat', $k->id_profil), false);
    }

    public function test_halaman_kosong_saat_tidak_ada_kandidat(): void
    {
        $pembuat = $this->profil('Pembuat');
        $p = $this->buatPost($pembuat, 'Public Speaking');

        $this->actingAs($pembuat->user)->get("/postingan/{$p->id_postingan}/rekomendasi")
            ->assertOk()->assertSee('Belum ada kandidat yang cocok');
    }

    public function test_user_lain_tamu_dan_postingan_biasa_ditolak(): void
    {
        $pembuat = $this->profil('Pembuat');
        $lain = $this->profil('Lain');
        $p = $this->buatPost($pembuat, 'Public Speaking');
        $this->actingAs($lain->user)->get("/postingan/{$p->id_postingan}/rekomendasi")->assertForbidden();
        $this->app['auth']->forgetGuards();
        $this->get("/postingan/{$p->id_postingan}/rekomendasi")->assertRedirect('/login');
        $biasa = $this->buatPost($pembuat, 'Public Speaking', 'Biasa');
        $this->actingAs($pembuat->user)->get("/postingan/{$biasa->id_postingan}/rekomendasi")->assertNotFound();
        $this->actingAs($pembuat->user)->get('/postingan/9999/rekomendasi')->assertNotFound();
    }

    public function test_user_tanpa_profil_diarahkan_ke_onboarding(): void
    {
        $pembuat = $this->profil('Pembuat');
        $p = $this->buatPost($pembuat, 'Public Speaking');
        $tanpa = User::create(['name' => 'T', 'tanggal_lahir' => '2004-01-01', 'email' => 't@student.ub.ac.id', 'no_telepon' => '0800', 'password' => 'rahasia123']);
        $this->actingAs($tanpa)->get("/postingan/{$p->id_postingan}/rekomendasi")->assertRedirect(route('onboarding.form'));
    }
}
