{{-- resources/views/components/panel-pesan.blade.php --}}
{{--
    Kolom Pesan (inbox) di sebelah kanan feed.
    Ditampilkan secara default di app.blade.php jika slot $inbox tidak diisi.
--}}

<aside class="kub-inbox hidden xl:block">
    <h2 class="flex items-start gap-[13px] text-body font-medium pl-[7px]" style="height:36px;line-height:24px">
        <x-icon name="message" class="w-[18px] h-[18px] mt-[3px]" />
        Pesan
    </h2>

    <ul class="flex flex-col gap-[17px] rounded-kub bg-surface" style="padding:19px 16px 23px">
        @forelse($percakapanTerbaru ?? [] as $chat)
            <li class="kub-chat">
                <x-avatar />
                <div>
                    <p class="text-body">{{ $chat->lawan->name ?? 'Pengguna' }}</p>
                    <p class="text-sm text-muted" style="margin-top:-5px">
                        {{ Str::limit($chat->pesanTerakhir->isi ?? 'Text Text Text Text', 30) }}
                    </p>
                </div>
            </li>
        @empty
            <li class="kub-chat">
                <x-avatar />
                <div>
                    <p class="text-body">Belum ada pesan</p>
                    <p class="text-sm text-muted" style="margin-top:-5px">Mulai percakapan baru</p>
                </div>
            </li>
        @endforelse
    </ul>
</aside>
