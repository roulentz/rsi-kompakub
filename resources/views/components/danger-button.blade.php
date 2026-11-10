{{-- resources/views/components/danger-button.blade.php --}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-1 rounded-pill bg-[#B00020] px-5 py-2.5 text-sm font-medium text-white tracking-wide hover:bg-red-800 transition']) }}>
    {{ $slot }}
</button>
