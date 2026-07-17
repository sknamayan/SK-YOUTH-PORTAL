@props([
    'href' => null,
    'type' => 'button',
    'form' => null,
])

{{--
    Mobile Bottom Action Button Component
    ==========================================================================
    A sticky, fixed-to-bottom action container visible only on mobile screens (md:hidden).
    Ensures primary calls-to-action remain thumb-accessible on small screens.
    
    Usage:
    ------
    1. Standard Button:
       <x-mobile-bottom-action onclick="alert('Clicked!')">
           Save Changes
       </x-mobile-bottom-action>
       
    2. Link Action:
       <x-mobile-bottom-action href="{{ route('projects.create') }}">
           Create Project
       </x-mobile-bottom-action>

    3. External Form Submission:
       <x-mobile-bottom-action form="edit-profile-form" type="submit">
           Save Changes
       </x-mobile-bottom-action>

    Note: Ensure you add 'pb-24' (or similar padding-bottom) to the page's main 
    wrapper container so scrolling content does not get clipped by this sticky bar.
--}}

<div class="fixed bottom-0 left-0 right-0 z-50 md:hidden bg-slate-900/95 border-t border-slate-800/80 backdrop-blur-sm shadow-[0_-4px_12px_rgba(0,0,0,0.15)]">
    <div class="px-4 py-3">
        @if($href)
            <a
                href="{{ $href }}"
                {{ $attributes->merge([
                    'class' => 'block w-full text-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 shadow-lg shadow-blue-950/25 transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900',
                ]) }}
            >
                {{ $slot }}
            </a>
        @elseif($form)
            <button
                type="{{ $type }}"
                form="{{ $form }}"
                {{ $attributes->merge([
                    'class' => 'w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 shadow-lg shadow-blue-950/25 transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900',
                ]) }}
            >
                {{ $slot }}
            </button>
        @else
            <button
                type="{{ $type }}"
                {{ $attributes->merge([
                    'class' => 'w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 shadow-lg shadow-blue-950/25 transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900',
                ]) }}
            >
                {{ $slot }}
            </button>
        @endif
    </div>
</div>
