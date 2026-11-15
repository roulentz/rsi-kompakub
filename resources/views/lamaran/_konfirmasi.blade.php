{{--
    Dialog "Konfirmasi Lamaran" (UI05, PCT-PC06, milik SH): muncul saat mahasiswa menekan Apply/Lamar.
    Variabel: $postingan (Cari Tim), $profil (pelamar). Nama dialog: konfirmasi-lamaran-{id_postingan}.
    Pemicu: $dispatch('open-modal', 'konfirmasi-lamaran-' . $postingan->id_postingan)
    "Ya, Ajukan" mengirim ke route lamaran.store.
--}}
@php
    $skillDicari = collect(explode(',', (string) $postingan->tag_skill))->map(fn ($s) => trim($s))->filter()->implode(', ');
    $terverifikasi = $profil->user->email_verified_at !== null;
@endphp

<x-modal name="konfirmasi-lamaran-{{ $postingan->id_postingan }}" maxWidth="md" focusable>
    <div class="p-6">
        <div class="flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand/20 text-brand-dark">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10" cy="10" r="7.5"/><path d="m6.8 10.2 2.2 2.2 4.2-4.4"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <h3 class="text-base font-bold text-navy">Konfirmasi Lamaran</h3>
                <p class="text-xs text-muted">KompakUB Mahasiswa Brawijaya</p>
            </div>
            <button type="button" x-on:click="$dispatch('close')" class="text-muted hover:text-navy" aria-label="Tutup">
                <x-icon name="x" class="h-4 w-4" />
            </button>
        </div>

        <dl class="mt-4 divide-y divide-gray-100 rounded-lg border border-gray-100 text-sm">
            <div class="flex justify-between gap-3 px-3 py-2"><dt class="shrink-0 text-muted">Lomba:</dt><dd class="text-right font-semibold text-navy">{{ $postingan->nama_lomba }}</dd></div>
            @if ($skillDicari !== '')
                <div class="flex justify-between gap-3 px-3 py-2"><dt class="shrink-0 text-muted">Skill Dicari:</dt><dd class="text-right font-semibold text-navy">{{ $skillDicari }}</dd></div>
            @endif
            <div class="flex justify-between gap-3 px-3 py-2"><dt class="shrink-0 text-muted">Pelamar:</dt><dd class="text-right font-semibold text-navy">{{ $profil->user->name }}{{ $profil->jurusan ? ' ('.$profil->jurusan.($profil->angkatan ? " '".substr((string) $profil->angkatan, -2) : '').')' : '' }}</dd></div>
            <div class="flex justify-between gap-3 px-3 py-2"><dt class="shrink-0 text-muted">Status Verifikasi:</dt><dd class="text-right font-semibold {{ $terverifikasi ? 'text-emerald-600' : 'text-red-600' }}">{{ $terverifikasi ? 'Mahasiswa Aktif UB' : 'Belum terverifikasi' }}</dd></div>
        </dl>

        <div class="mt-5 flex justify-end gap-2">
            <button type="button" x-on:click="$dispatch('close')" class="rounded-full border border-gray-300 bg-white px-5 py-1.5 text-sm font-medium text-navy hover:bg-surface">Batal</button>
            <form method="POST" action="{{ route('lamaran.store', $postingan->id_postingan) }}">
                @csrf
                <button type="submit" class="rounded-full bg-brand px-5 py-1.5 text-sm font-medium text-white transition hover:bg-brand-dark">Ya, Ajukan</button>
            </form>
        </div>
    </div>
</x-modal>
