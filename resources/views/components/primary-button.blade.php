<button {{ $attributes->merge(['type' => 'submit', 'class' => 'kub-btn kub-btn-brand']) }}>
    {{ $slot }}
</button>
