
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-navy-deep">Pesan</h2>
    </x-slot>

    <div class="mx-auto w-full max-w-5xl px-4 sm:px-6">
        <div class="flex h-[calc(100vh-230px)] min-h-[480px] flex-col overflow-hidden rounded-2xl border border-navy/10 bg-white shadow-sm">

            {{-- Header percakapan --}}
            <div class="flex shrink-0 items-center gap-3 bg-navy px-5 py-4">
                <x-avatar
                    :user="$lawan"
                    :foto="$lawan->profil?->fotoUrl()"
                    size="h-11 w-11 text-sm"
                />

                <div class="min-w-0">
                <a href="{{ $lawan->profil ? route('profil.lihat', $lawan->profil) : '#' }}"
                 class="block truncate text-base font-semibold text-white hover:underline">
               {{ $lawan->name }}
                </a>
                </div>
            </div>

            {{-- Area pesan --}}
            <div
                id="area-pesan"
                class="flex-1 space-y-3 overflow-y-auto bg-white px-5 py-5"
                aria-label="Riwayat percakapan"
            ></div>

            {{-- Input pesan --}}
            <form id="form-pesan"
                  class="flex shrink-0 items-center gap-3 border-t border-gray-100 bg-white p-4">

                <input
                    type="text"
                    id="isi_pesan"
                    maxlength="1000"
                    required
                    autocomplete="off"
                    class="min-w-0 flex-1 rounded-xl border-gray-300 text-sm focus:border-brand focus:ring-brand"
                    placeholder="Tulis pesan..."
                >

                <button
                    type="submit"
                    id="tombol-kirim"
                    class="kub-btn kub-btn-brand shrink-0"
                >
                    <x-icon name="chat" class="h-4 w-4" />
                    Kirim
                </button>
            </form>

            <p id="pesan-error"
               class="hidden px-4 pb-3 text-sm text-red-600"
               role="alert"></p>
        </div>
    </div>

    <script>
        const urlPesan = @json(route('dm.pesan', $percakapan));
        const urlKirim = @json(route('dm.kirim', $percakapan));
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const area = document.getElementById('area-pesan');
        const form = document.getElementById('form-pesan');
        const input = document.getElementById('isi_pesan');
        const tombol = document.getElementById('tombol-kirim');
        const error = document.getElementById('pesan-error');

        function gelembung(p) {
            const wrap = document.createElement('div');
            wrap.className = 'flex ' + (p.milik_saya ? 'justify-end' : 'justify-start');

            const isi = document.createElement('div');
            isi.className = 'max-w-[80%] rounded-2xl px-4 py-2.5 text-sm shadow-sm '
                + (p.milik_saya
                    ? 'bg-navy text-white'
                    : 'bg-surface-tint text-navy-deep');

            const teks = document.createElement('p');
            teks.className = 'whitespace-pre-wrap break-words';
            teks.textContent = p.isi_pesan;

            const jam = document.createElement('p');
            jam.className = 'mt-1 text-right text-[11px] opacity-65';
            jam.textContent = p.waktu_kirim;

            isi.append(teks, jam);
            wrap.appendChild(isi);

            return wrap;
        }

        async function muatPesan() {
            try {
                const res = await fetch(urlPesan, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!res.ok) return;

                const daftar = await res.json();
                const dekatBawah = area.scrollHeight - area.scrollTop - area.clientHeight < 80;

                area.replaceChildren(...daftar.map(gelembung));

                if (dekatBawah) {
                    area.scrollTop = area.scrollHeight;
                }
            } catch (e) {
                console.error('Gagal memuat pesan:', e);
            }
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const isi = input.value.trim();
            if (!isi || tombol.disabled) return;

            tombol.disabled = true;
            error.classList.add('hidden');

            try {
                const res = await fetch(urlKirim, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ isi_pesan: isi })
                });

                if (!res.ok) {
                    throw new Error('Pesan gagal dikirim.');
                }

                input.value = '';
                await muatPesan();
                area.scrollTop = area.scrollHeight;
            } catch (e) {
                error.textContent = e.message;
                error.classList.remove('hidden');
            } finally {
                tombol.disabled = false;
                input.focus();
            }
        });

        muatPesan();
        setInterval(muatPesan, 5000);
    </script>
</x-app-layout>
