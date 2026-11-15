<?php

namespace Tests\Feature;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function buatUser(): User
    {
        return User::create([
            'name' => 'Nina', 'tanggal_lahir' => '2005-01-01', 'email' => 'nina@student.ub.ac.id',
            'no_telepon' => '0800', 'password' => 'rahasia123',
        ]);
    }

    public function test_data_wizard_langsung_tersimpan_di_profil(): void
    {
        Storage::fake('public');
        $user = $this->buatUser();
        $skill = Skill::create(['nama_skill' => 'UI/UX Design']);

        $this->actingAs($user)->post('/onboarding', [
            'aksi' => 'lengkapi',
            'jurusan' => 'Sistem Informasi, FILKOM',
            'angkatan' => '2025',
            'bio' => 'Suka hackathon.',
            'interest' => 'hackathon',
            'skills' => [$skill->id_skill],
            'foto' => UploadedFile::fake()->image('foto.jpg', 400, 400),
        ])->assertRedirect(route('profil.show'));

        $profil = $user->fresh()->profil;
        $this->assertSame('Sistem Informasi, FILKOM', $profil->jurusan);
        $this->assertSame('2025', $profil->angkatan);
        $this->assertSame('Suka hackathon.', $profil->bio);
        $this->assertSame('hackathon', $profil->interest);
        $this->assertFalse($profil->status_default);
        $this->assertCount(1, $profil->skills);
        Storage::disk('public')->assertExists($profil->foto);
    }

    public function test_lewati_di_tengah_wizard_tetap_menyimpan_yang_sudah_diisi(): void
    {
        $user = $this->buatUser();

        $this->actingAs($user)->post('/onboarding', ['aksi' => 'lewati', 'jurusan' => 'Informatika'])
            ->assertRedirect(route('profil.show'));

        $profil = $user->fresh()->profil;
        $this->assertSame('Informatika', $profil->jurusan);
        $this->assertNull($profil->bio);
        $this->assertFalse($profil->status_default);
    }

    public function test_lewati_tanpa_isian_membuat_profil_kosong(): void
    {
        $user = $this->buatUser();

        $this->actingAs($user)->post('/onboarding', ['aksi' => 'lewati'])
            ->assertRedirect(route('dashboard'));

        $this->assertTrue($user->fresh()->profil->status_default);
    }
}
