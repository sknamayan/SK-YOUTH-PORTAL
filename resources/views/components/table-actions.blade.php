@props(['id', 'routePrefix', 'isArchived' => false])

<div class="inline-flex items-center space-x-1.5 whitespace-nowrap">
    {{-- View Button --}}
    <a href="{{ route($routePrefix . '.show', $id) }}"
       class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg font-bold text-[9px] uppercase tracking-wider transition">
        View
    </a>

    {{-- Edit Button --}}
    <a href="{{ route($routePrefix . '.edit', $id) }}"
       class="inline-flex items-center px-2 py-1 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-lg font-bold text-[9px] uppercase tracking-wider transition">
        Edit
    </a>

    {{-- Archive/Unarchive Toggle --}}
    <form action="{{ route($routePrefix . '.toggle-archive', $id) }}" method="POST" class="inline">
        @csrf
        @method('POST')
        <button type="submit"
                class="inline-flex items-center px-2 py-1 {{ $isArchived ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} rounded-lg font-bold text-[9px] uppercase tracking-wider transition cursor-pointer">
            {{ $isArchived ? 'Restore' : 'Archive' }}
        </button>
    </form>

    {{-- Delete Button (Soft-Delete) --}}
    <x-action-delete :id="$id" :route="route($routePrefix . '.destroy', $id)" />
</div>
