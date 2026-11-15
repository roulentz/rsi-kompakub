<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $lawan->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <div id="area-pesan" class="h-96 overflow-y-auto space-y-2 mb-4"></div>

                <form id="form-pesan" class="flex gap-2">
                    <input type="text" id="isi_pesan" maxlength="1000" required
                           class="flex-1 border-gray-300 rounded-md" placeholder="Tulis pesan">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">Kirim</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const urlPesan = @json(route('dm.pesan', $percakapan));
        const urlKirim = @json(route('dm.kirim', $percakapan));
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const area = document.getElementById('area-pesan');

        function gelembung(p) {
            const wrap = document.createElement('div');
            wrap.className = 'flex ' + (p.milik_saya ? 'justify-end' : 'justify-start');

            const isi = document.createElement('div');
            isi.className = 'max-w-xs px-3 py-2 rounded-lg text-sm '
                + (p.milik_saya ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-900');
            isi.textContent = p.isi_pesan;

            const jam = document.createElement('div');
            jam.className = 'text-xs opacity-70 mt-1';
            jam.textContent = p.waktu_kirim;

            isi.appendChild(jam);
            wrap.appendChild(isi);
            return wrap;
        }

        async function muatPesan() {
            const res = await fetch(urlPesan, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const daftar = await res.json();
            const dekatBawah = area.scrollHeight - area.scrollTop - area.clientHeight < 80;
            area.replaceChildren(...daftar.map(gelembung));
            if (dekatBawah) area.scrollTop = area.scrollHeight;
        }

        document.getElementById('form-pesan').addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = document.getElementById('isi_pesan');
            const isi = input.value.trim();
            if (!isi) return;

            await fetch(urlKirim, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ isi_pesan: isi }),
            });

            input.value = '';
            muatPesan();
        });

        muatPesan();
        setInterval(muatPesan, 5000);
    </script>
</x-app-layout>