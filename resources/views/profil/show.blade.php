@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">{{ $pemilik ? 'Profil Saya' : 'Profil '.$profil->user->name }}</h2></x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            @if ($pemilik && $profil->status_default)
                <div class="rounded-md bg-yellow-50 p-3 text-sm text-yellow-800">
                    Profil Anda masih kosong. <a href="{{ route('profil.edit') }}" class="underline">Lengkapi sekarang</a>.
                </div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg flex gap-4">
                @if ($profil->fotoUrl())
                    <img src="{{ $profil->fotoUrl() }}" alt="Foto" class="h-24 w-24 rounded-full object-cover">
                @else
                    <div class="h-24 w-24 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-3xl font-semibold">{{ strtoupper(substr($profil->user->name, 0, 1)) }}</div>
                @endif
                <div class="flex-1">
                    <h3 class="text-xl font-semibold">{{ $profil->user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $profil->jurusan ?: 'Jurusan belum diisi' }}{{ $profil->angkatan ? ' · Angkatan '.$profil->angkatan : '' }}</p>
                    <p class="mt-2 text-gray-700">{{ $profil->bio ?: 'Belum ada bio.' }}</p>
                    @if ($profil->interest)<p class="mt-2 text-sm"><span class="font-medium">Interest:</span> {{ $profil->interest }}</p>@endif
                    <div class="mt-3 flex flex-wrap gap-2">
                        @forelse ($profil->skills as $s)
                            <span class="px-2 py-1 text-xs rounded-full bg-indigo-50 text-indigo-700">{{ $s->nama_skill }}</span>
                        @empty
                            <span class="text-sm text-gray-400">Belum ada skill.</span>
                        @endforelse
                    </div>
                </div>
                @if ($pemilik)
                    <a href="{{ route('profil.edit') }}" class="self-start px-3 py-2 border rounded-md text-sm">Edit Profil</a>
                @endif
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold">Portofolio</h3>
                    @if ($pemilik)<a href="{{ route('portofolio.edit') }}" class="text-sm underline">Kelola</a>@endif
                </div>
                @php $slides = $profil->portofolio->map(fn ($p) => ['src' => Storage::disk('public')->url($p->konten), 'link' => $p->tautan_eksternal])->values(); @endphp
                @if ($slides->isEmpty())
                    <p class="text-sm text-gray-400">Belum ada portofolio.</p>
                @else
                    <div x-data="{ i: 0, s: @js($slides) }">
                        <img :src="s[i].src" class="w-full max-h-96 object-contain bg-gray-50 rounded">
                        <div class="mt-2 flex items-center justify-between text-sm">
                            <button type="button" @click="i = (i - 1 + s.length) % s.length" class="px-3 py-1 border rounded">&larr;</button>
                            <span><span x-text="i + 1"></span> / <span x-text="s.length"></span>
                                <template x-if="s[i].link"><span> · <a :href="s[i].link" target="_blank" rel="noopener" class="underline">Tautan</a></span></template>
                            </span>
                            <button type="button" @click="i = (i + 1) % s.length" class="px-3 py-1 border rounded">&rarr;</button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="font-semibold mb-3">Riwayat Kolaborasi</h3>
                @forelse ($riwayat as $r)
                    <div class="py-2 border-b last:border-0 text-sm">
                        <span class="font-medium">{{ $r->judul_proyek }}</span>
                        <span class="text-gray-500">· {{ $r->peran }} · {{ $r->tanggal_selesai->translatedFormat('d M Y') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada riwayat kolaborasi.</p>
                @endforelse
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="font-semibold mb-3">Rekomendasi</h3>
                @forelse ($rekomendasi as $rk)
                    <div class="py-3 border-b last:border-0">
                        <p class="text-sm text-gray-800">{{ $rk->isi_rekomendasi }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Dari <a href="{{ route('profil.lihat', $rk->pemberi) }}" class="underline">{{ $rk->pemberi->user->name }}</a>
                            · {{ $rk->tanggal_dikirim->translatedFormat('d M Y') }}
                            @if ($pemilik && $rk->status_laporan === 'dilaporkan') · <span class="text-yellow-700">Sedang ditinjau admin</span> @endif
                        </p>
                        @if ($pemilik && $rk->status_laporan === 'tidak_dilaporkan')
                            <form method="POST" action="{{ route('rekomendasi.laporkan', $rk) }}" class="mt-1" onsubmit="return confirm('Laporkan rekomendasi ini ke admin?')">
                                @csrf
                                <button class="text-xs text-red-600 underline">Laporkan</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada rekomendasi.</p>
                @endforelse

                @if ($bolehRekomendasi)
                    <form method="POST" action="{{ route('rekomendasi.store', $profil) }}" class="mt-4">
                        @csrf
                        <textarea name="isi_rekomendasi" rows="3" maxlength="500" required class="w-full rounded-md border-gray-300" placeholder="Tulis rekomendasi (maks. 500 karakter)">{{ old('isi_rekomendasi') }}</textarea>
                        <x-input-error :messages="$errors->get('isi_rekomendasi')" class="mt-1" />
                        <x-primary-button class="mt-2">Kirim Rekomendasi</x-primary-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
