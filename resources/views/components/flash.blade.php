{{-- resources/views/components/flash.blade.php --}}
{{-- Flash message notification --}}

@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="kub-toast">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="kub-warning">
        {{ session('error') }}
    </div>
@endif
