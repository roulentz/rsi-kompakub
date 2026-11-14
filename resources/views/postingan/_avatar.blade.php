{{-- Avatar mahasiswa: foto kalau ada, kalau tidak dua huruf nama. --}}
@php $ukuran = $ukuran ?? 'h-11 w-11 text-sm'; @endphp

@if ($profil->foto)
    <img src="{{ $profil->fotoUrl() }}" alt="Foto {{ $profil->user->name }}" class="{{ $ukuran }} shrink-0 rounded-full border-2 border-navy object-cover">
@else
    <span class="{{ $ukuran }} flex shrink-0 items-center justify-center rounded-full border-2 border-navy font-semibold text-navy" aria-hidden="true">
        {{ strtoupper(mb_substr($profil->user->name, 0, 2)) }}
    </span>
@endif
