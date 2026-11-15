<button {{ $attributes->merge(['type' => 'submit', 'class' => 'kub-btn kub-btn-danger']) }}>
    {{ $slot }}
</button>
