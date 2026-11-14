@if (session('status'))
    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700" role="status">{{ session('status') }}</div>
@endif
