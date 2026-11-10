@props(['postingan'])

{{-- Pemakaian di view postingan: <x-tombol-selesai :postingan="$postingan" /> --}}
@if ($postingan->kategori === 'Cari Tim' && $postingan->status === 'Aktif' && auth()->user()->profil?->id_profil === $postingan->id_pembuat)
    <form method="POST" action="{{ route('postingan.selesai', $postingan) }}"
          onsubmit="return confirm('Tandai proyek ini selesai? Riwayat kolaborasi dicatat untuk semua anggota yang diterima.')">
        @csrf
        <button class="px-3 py-1 bg-green-600 text-white rounded-md text-sm">Tandai Selesai</button>
    </form>
@endif
