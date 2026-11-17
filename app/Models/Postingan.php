<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Postingan extends Model
{
    protected $table = 'postingan';

    protected $primaryKey = 'id_postingan';

    protected $fillable = [
        'id_pembuat',
        'kategori',
        'nama_lomba',
        'waktu_perlombaan',
        'tag_skill',
        'jumlah_anggota',
        'deskripsi',
        'foto',
        'status',
        'pencapaian',
        'nama_proyek',
    ];

    /** Pilihan pencapaian yang diisi pembuat saat proyek ditandai selesai. */
    public const PENCAPAIAN = ['Juara 1', 'Juara 2', 'Juara 3', 'Harapan', 'Finalis', 'Peserta'];

    /** Maksimal foto per postingan. */
    public const MAKS_FOTO = 10;

    // Foto dimuat bersama postingan (satu query tambahan per halaman, bukan per kartu).
    protected $with = ['fotoPostingan'];

    protected static function booted(): void
    {
        // Hapus berkas foto dari penyimpanan saat postingan dihapus (barisnya terhapus lewat FK cascade).
        static::deleting(function (Postingan $p) {
            $berkas = $p->fotoPostingan->pluck('path')->push($p->foto)->filter()->unique()->all();
            if ($berkas !== []) {
                Storage::disk('public')->delete($berkas);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'waktu_perlombaan' => 'date',
            'jumlah_anggota' => 'integer',
        ];
    }

    /** Tombol Tandai Selesai baru aktif pada hari lomba atau setelahnya (hasil pengumuman sudah ada). */
    public function lombaSudahLewat(): bool
    {
        return $this->waktu_perlombaan === null || $this->waktu_perlombaan->lte(today());
    }

    public function fotoPostingan(): HasMany
    {
        return $this->hasMany(FotoPostingan::class, 'id_postingan', 'id_postingan')->orderBy('urutan')->orderBy('id_foto');
    }

    /** Alamat semua foto (relatif, agar tampil di host dan port mana pun). Postingan lama hanya punya kolom foto. */
    public function daftarFoto(): array
    {
        $jalur = $this->fotoPostingan->pluck('path')->all();

        if ($jalur === [] && $this->foto) {
            $jalur = [$this->foto];
        }

        return array_map(fn ($p) => '/storage/'.$p, $jalur);
    }

    /** Simpan foto-foto yang diunggah (urutan sesuai pilihan). Foto pertama juga jadi sampul di kolom foto. */
    public function simpanFoto(array $berkas): void
    {
        foreach (array_values($berkas) as $urutan => $file) {
            $path = $file->store('postingan', 'public');
            $this->fotoPostingan()->create(['path' => $path, 'urutan' => $urutan]);

            if ($urutan === 0) {
                $this->update(['foto' => $path]);
            }
        }

        $this->unsetRelation('fotoPostingan');
    }

    /**
     * Aturan validasi foto: menerima satu berkas (cara lama) atau sampai 10 berkas (foto[]).
     * Tiap foto maksimal 2 MB (browser memperkecil foto besar sebelum dikirim).
     */
    public static function aturanFoto(Request $request): array
    {
        if (is_array($request->file('foto'))) {
            return [
                'foto' => ['nullable', 'array', 'max:'.self::MAKS_FOTO],
                'foto.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            ];
        }

        return ['foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048']];
    }

    public static function pesanFoto(Request $request): array
    {
        $pesan = [
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'foto.*.image' => 'Setiap file harus berupa gambar.',
            'foto.*.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'foto.*.max' => 'Ukuran tiap foto maksimal 2 MB.',
        ];

        $pesan['foto.max'] = is_array($request->file('foto'))
            ? 'Maksimal '.self::MAKS_FOTO.' foto per postingan.'
            : 'Ukuran foto maksimal 2 MB.';

        return $pesan;
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(
            ProfilMahasiswa::class,
            'id_pembuat',
            'id_profil'
        );
    }

    public function lamaran(): HasMany
    {
        return $this->hasMany(
            Lamaran::class,
            'id_postingan',
            'id_postingan'
        );
    }

    // Slot terisi = pembuat postingan + pelamar yang diterima (sama seperti UI03)
    public function slotTerisi(): int
    {
        return 1 + $this->lamaran()->where('status', 'Diterima')->count();
    }

    // PCT-PC01: profil yang skill-nya cocok dengan tag_skill (maksimal $batas profil)
    public function rekomendasiKandidat(int $batas = 10): Collection
    {
        $namaSkill = collect(explode(',', (string) $this->tag_skill))
            ->map(fn ($skill) => trim($skill))
            ->filter()
            ->unique()
            ->values();

        if ($namaSkill->isEmpty()) {
            return new Collection();
        }

        return ProfilMahasiswa::query()
            ->where('id_profil', '!=', $this->id_pembuat)
            ->whereHas('skills', fn ($query) => $query->whereIn('nama_skill', $namaSkill))
            ->withCount([
                'skills as skill_cocok' => fn ($query) => $query->whereIn('nama_skill', $namaSkill),
            ])
            ->with(['user:id,name', 'skills'])
            ->orderByDesc('skill_cocok')
            ->orderBy('id_profil')
            ->limit($batas)
            ->get();
    }
}
