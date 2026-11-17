{{--
    Floating support button, bottom right of the screen. Tapping it opens the support links upwards
    (WhatsApp, Telegram, Bale), one after another; tapping again, outside, or Esc closes them.
    On phones it sits above the bottom bar. Links come from .env SUPPORT_* (see App\Support\SupportLinks);
    with no links at all (production, nothing set) the button is not shown.
--}}
@php
    $links = \App\Support\SupportLinks::all();
    // Brand colours of each app, so they are recognised at a glance.
    $colors = ['whatsapp' => 'bg-[#25D366]', 'telegram' => 'bg-[#2AABEE]', 'bale' => 'bg-[#3BA79A]'];
@endphp

@if ($links)
    <div x-data="{ open: false }"
         x-on:keydown.escape.window="open = false"
         x-on:click.outside="open = false"
         class="fixed right-4 bottom-[calc(5rem+env(safe-area-inset-bottom))] z-40 flex flex-col items-start gap-3 md:right-6 md:bottom-6">

        {{-- Links: rise from the button one by one (the nearest first) --}}
        {{-- RTL: "start" is the right edge, so links line up above the button; icon first = icon on the right. --}}
        <ul id="support-links" class="flex flex-col items-start gap-3" aria-label="{{ __('site.support_title') }}">
            @foreach (array_reverse($links) as $i => $link)
                @php($delay = (count($links) - 1 - $i) * 60)
                <li x-show="open" x-cloak
                    x-transition:enter="transition duration-300 ease-out"
                    x-transition:enter-start="translate-y-4 scale-75 opacity-0"
                    x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                    x-transition:leave="transition duration-150 ease-in"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="translate-y-2 opacity-0"
                    style="transition-delay: {{ $delay }}ms">
                    <a href="{{ $link['url'] }}" @if ($link['url'] !== '#') target="_blank" rel="noopener" @endif
                       class="group flex items-center gap-3"
                       aria-label="{{ __('site.support_via', ['name' => __('site.support_'.$link['key'])]) }}">
                        <span class="ms-1 flex size-12 shrink-0 items-center justify-center rounded-full text-white shadow-[0_8px_22px_-8px_rgba(30,26,22,0.6)] transition-transform group-hover:scale-110 {{ $colors[$link['key']] }}" aria-hidden="true">
                            @switch($link['key'])
                                @case('whatsapp')
                                    <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4.2 19.8l1.1-3.9A8.3 8.3 0 1 1 8.4 19l-4.2.8z"/><path d="M9.2 8.4c.2-.4.4-.4.7-.4h.5c.2 0 .4 0 .5.4l.7 1.6c.1.2 0 .4-.1.6l-.5.6c-.1.1-.2.3 0 .5.6 1 1.4 1.8 2.5 2.4.2.1.4.1.5-.1l.6-.7c.2-.2.4-.2.6-.1l1.6.8c.2.1.3.3.3.5 0 .9-.7 1.7-1.7 1.8-.9.1-2.6-.3-4.3-2-1.7-1.6-2.3-3.3-2.2-4.2 0-.7.3-1.2.3-1.7z" fill="currentColor" stroke="none"/></svg>
                                    @break
                                @case('telegram')
                                    <svg viewBox="0 0 24 24" class="size-6" fill="currentColor"><path d="M21.6 4.2 18.4 19.6c-.2 1.1-.9 1.4-1.8.9l-5-3.7-2.4 2.3c-.3.3-.5.5-1 .5l.4-5.1 9.2-8.3c.4-.4-.1-.6-.6-.2L5.8 13.2l-4.9-1.5c-1.1-.3-1.1-1 .2-1.5L20.2 2.8c.9-.3 1.7.2 1.4 1.4z"/></svg>
                                    @break
                                @default
                                    <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><path d="M12 3.5c-4.7 0-8.5 3.4-8.5 7.6 0 2.3 1.1 4.3 2.9 5.7l-.6 3.7 3.6-2c.8.2 1.7.3 2.6.3 4.7 0 8.5-3.4 8.5-7.7S16.7 3.5 12 3.5z"/><path d="M8.5 11.2l2.4 2.3 4.6-4.6" stroke-linecap="round"/></svg>
                            @endswitch
                        </span>
                        <span class="rounded-full bg-paper px-3 py-1.5 text-sm font-bold text-ink shadow-[0_6px_20px_-8px_rgba(30,26,22,0.45)] transition-colors group-hover:text-gold-deep" aria-hidden="true">
                            {{ __('site.support_'.$link['key']) }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- The bubble itself: chat icon, turns into × when open --}}
        <button type="button" x-on:click="open = ! open"
                x-bind:aria-expanded="open.toString()" aria-expanded="false" aria-controls="support-links"
                x-bind:aria-label="open ? @js(__('site.support_close')) : @js(__('site.support_open'))"
                aria-label="{{ __('site.support_open') }}"
                class="relative flex size-14 items-center justify-center rounded-full border border-gold/40 bg-ink text-gold shadow-[0_12px_30px_-10px_rgba(30,26,22,0.7)] transition duration-300 hover:scale-105 hover:border-gold active:scale-95 motion-reduce:transform-none">
            {{-- Soft pulse ring while closed, to be noticed (not for reduced motion) --}}
            <span x-show="! open" class="absolute inset-0 rounded-full border-2 border-gold/50 motion-safe:animate-ping [animation-duration:2.4s]" aria-hidden="true"></span>

            <svg x-show="! open" x-transition:enter="transition duration-300" x-transition:enter-start="rotate-90 scale-50 opacity-0" x-transition:enter-end="rotate-0 scale-100 opacity-100"
                 viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 11.5c0 4.1-3.6 7.5-8 7.5-1 0-2-.2-2.9-.5L4 20l1.4-3.8A7.2 7.2 0 0 1 4 11.5C4 7.4 7.6 4 12 4s8 3.4 8 7.5z"/>
                <path d="M8.5 11.5h.01M12 11.5h.01M15.5 11.5h.01" stroke-width="2.4"/>
            </svg>
            <svg x-show="open" x-cloak x-transition:enter="transition duration-300" x-transition:enter-start="-rotate-90 scale-50 opacity-0" x-transition:enter-end="rotate-0 scale-100 opacity-100"
                 viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <path d="M6 6l12 12M18 6 6 18"/>
            </svg>
        </button>
    </div>
@endif
