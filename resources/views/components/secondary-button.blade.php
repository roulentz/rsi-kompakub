{{-- resources/views/components/secondary-button.blade.php --}}
<button {{ $attributes->merge(['type' => 'button', 'class' => 'kub-btn kub-btn-ghost px-5 py-2.5 text-sm font-medium tracking-wide']) }}>
    {{ $slot }}
</button>
