{{-- resources/views/components/primary-button.blade.php --}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'kub-btn kub-btn-navy px-5 py-2.5 text-sm font-medium tracking-wide']) }}>
    {{ $slot }}
</button>
