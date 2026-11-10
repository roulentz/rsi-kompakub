{{-- resources/views/components/empty-state.blade.php --}}
{{--
    Kondisi kosong (empty state).
    Props:
    - $icon  : string — nama ikon
    - $title : string
    - $text  : string
--}}
@props([
    'icon'  => 'users',
    'title' => 'Belum ada data',
    'text'  => '',
])

<section class="kub-box kub-empty">
    <div class="kub-empty-icon">
        <x-icon :name="$icon" class="w-4 h-4" />
    </div>
    <h3 class="kub-empty-title">{{ $title }}</h3>
    @if($text)
        <p class="kub-empty-text">{{ $text }}</p>
    @endif
    {{ $slot }}
</section>
