<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg border border-navy/30 bg-white px-5 py-2.5 text-sm font-semibold text-navy shadow-sm transition hover:bg-surface-tint focus:outline-none focus:ring-2 focus:ring-navy focus:ring-offset-2 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
