{{-- resources/views/components/dropdown-link.blade.php --}}
<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm text-navy hover:bg-surface transition']) }}>
    {{ $slot }}
</a>
