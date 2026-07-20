@props(['id', 'route'])

<form action="{{ $route }}" method="POST" class="inline delete-action-form" data-id="{{ $id }}">
    @csrf
    @method('DELETE')
    <button type="submit" class="inline-flex items-center px-2 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-lg font-bold text-[9px] uppercase tracking-wider transition cursor-pointer">
        Delete
    </button>
</form>
