<nav class="header print:hidden">
    <!-- App Header  -->
    <div class="header-container relative flex w-full bg-white dark:bg-navy-750 print:hidden">
        <!-- Header Items -->
        <div class="flex w-full items-center justify-between">
            <!-- Left: Sidebar Toggle Button -->
            <div class="size-7">
                <button
                    class="menu-toggle cursor-pointer ml-0.5 flex size-7 flex-col justify-center space-y-1.5 text-primary outline-hidden focus:outline-hidden dark:text-accent-light/80"
                    :class="$store.global.isSidebarExpanded && 'active'"
                    @click="$store.global.isSidebarExpanded = !$store.global.isSidebarExpanded">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            <!-- Right: Header buttons -->
            <div class="-mr-1.5 flex items-center space-x-2">
                <!-- Mobile Search Toggle -->
                <button @click="$store.global.isSearchbarActive = !$store.global.isSearchbarActive"
                    class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25 sm:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5.5 text-slate-500 dark:text-navy-100"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>


                <!-- Dark Mode Toggle -->
                <button @click="$store.global.isDarkModeEnabled = !$store.global.isDarkModeEnabled"
                    class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                    <svg x-show="$store.global.isDarkModeEnabled"
                        x-transition:enter="transition-transform duration-200 ease-out absolute origin-top"
                        x-transition:enter-start="scale-75" x-transition:enter-end="scale-100 static"
                        class="size-6 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M11.75 3.412a.818.818 0 01-.07.917 6.332 6.332 0 00-1.4 3.971c0 3.564 2.98 6.494 6.706 6.494a6.86 6.86 0 002.856-.617.818.818 0 011.1 1.047C19.593 18.614 16.218 21 12.283 21 7.18 21 3 16.973 3 11.956c0-4.563 3.46-8.31 7.925-8.948a.818.818 0 01.826.404z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" x-show="!$store.global.isDarkModeEnabled"
                        x-transition:enter="transition-transform duration-200 ease-out absolute origin-top"
                        x-transition:enter-start="scale-75" x-transition:enter-end="scale-100 static"
                        class="size-6 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                            clip-rule="evenodd" />
                    </svg>
                </button> <!-- Monochrome Mode Toggle -->
                <button @click="$store.global.isMonochromeModeEnabled = !$store.global.isMonochromeModeEnabled"
                    class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                    <i
                        class="fa-solid fa-palette bg-linear-to-r from-sky-400 to-blue-600 bg-clip-text text-lg font-semibold text-transparent"></i>
                </button>


@php
    use App\Models\Alerte;
    use App\Models\Membre;
    use Illuminate\Support\Facades\Auth;

    $userId = Auth::id();
    $membre = Membre::where('user_id', $userId)->first();

    $alertes = $membre 
        ? Alerte::where('membre_id', $membre->id)
            ->where('etat', 1)
            ->latest('datealerte')
            ->take(10)
            ->get()
        : collect();

    $alertesNonLues = $alertes->where('lu', 0);
@endphp

<!-- Notifications -->
<div 
    x-effect="if($store.global.isSearchbarActive) isShowPopper = false" 
    x-data="usePopper({ placement: 'bottom-end', offset: 12 })"
    @click.outside="if(isShowPopper) isShowPopper = false" 
    class="flex"
>
    <!-- Bouton de notification -->
    <button 
        @click="isShowPopper = !isShowPopper" 
        x-ref="popperRef"
        class="btn relative size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-slate-500 dark:text-navy-100"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M15.375 17.556h-6.75M21 17.556h-6.75M8.625 17.556H3l1.58-1.562a2.254 2.254 0 00.67-1.596v-3.51a6.612 6.612 0 011.238-3.85 6.744 6.744 0 013.262-2.437v-.379a2.23 2.23 0 01.659-1.572A2.265 2.265 0 0112 2a2.265 2.265 0 011.591.65c.422.417.659.981.659 1.571v.38a6.744 6.744 0 013.262 2.437 6.612 6.612 0 011.238 3.85v3.51c0 .598.24 1.172.67 1.595L21 17.556zM9.375 18.666v1.11A3.397 3.397 0 0012 22a3.397 3.397 0 002.625-1.224 3.313 3.313 0 00.989-2.357v-1.111H9.375z" />
        </svg>

        @if($alertesNonLues->count() > 0)
        <span class="absolute -top-px -right-px flex size-3 items-center justify-center">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-secondary opacity-80"></span>
            <span class="inline-flex size-2 rounded-full bg-secondary"></span>
        </span>
        @endif
    </button>

    <!-- Liste déroulante -->
    <div :class="isShowPopper && 'show'" class="popper-root" x-ref="popperRoot">
        <div 
            class="popper-box mx-4 mt-1 flex max-h-[calc(100vh-6rem)] w-[calc(100vw-2rem)] flex-col 
            rounded-lg border border-slate-150 bg-white shadow-soft dark:border-navy-800 
            dark:bg-navy-700 dark:shadow-soft-dark sm:m-0 sm:w-80"
        >
            <div class="rounded-t-lg bg-slate-100 text-slate-600 dark:bg-navy-800 dark:text-navy-200">
                <div class="flex items-center justify-between px-4 pt-2">
                    <div class="flex items-center space-x-2">
                        <h3 class="font-medium text-slate-700 dark:text-navy-100">
                            Notifications
                        </h3>
                        <div class="badge h-5 rounded-full bg-primary/10 px-1.5 text-primary dark:bg-accent-light/15 dark:text-accent-light">
                            {{ $alertesNonLues->count() }}
                        </div>
                    </div><br /><br />
                </div><br />

                                <!-- <div class="is-scrollbar-hidden flex shrink-0 overflow-x-auto px-3">
                                    <button @click="activeTab = 'tabAlerts'"
                                        :class="activeTab === 'tabAlerts' ?
                                            'border-primary dark:border-accent text-primary dark:text-accent-light' :
                                            'border-transparent hover:text-slate-800 focus:text-slate-800 dark:hover:text-navy-100 dark:focus:text-navy-100'"
                                        class="btn shrink-0 rounded-none border-b-2 px-3.5 py-2.5">
                                        <span>Alertes</span>
                                    </button>
                                </div> -->
                            </div>

            <!-- Contenu des alertes -->
            <div class="is-scrollbar-hidden space-y-4 overflow-y-auto px-4 py-4">
    @forelse($alertesNonLues as $alerte)
        <a href="{{ route('recompense.voir', $alerte->id) }}" 
            class="flex items-start space-x-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-600 transition"
        >
            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg 
                {{ $alerte->lu ? 'bg-slate-200 dark:bg-navy-600' : 'bg-success/10 dark:bg-success/15' }}">
                <i class="fa fa-bell {{ $alerte->lu ? 'text-slate-400' : 'text-success' }}"></i>
            </div>
            <div class="flex-1">
                <p class="font-medium text-slate-700 dark:text-navy-100">
                    {{ $alerte->titre }}
                </p>
                <p class="mt-1 text-xs text-slate-400 dark:text-navy-300 line-clamp-2">
                    {!! $alerte->contenu !!}
                </p>
                <p class="mt-1 text-[10px] text-slate-400 dark:text-navy-400">
                    {{ \Carbon\Carbon::parse($alerte->updated_at)->diffForHumans() }}
                </p>
            </div>
        </a>
    @empty
        <div class="mt-4 pb-4 text-center text-slate-400 dark:text-navy-300">
            Aucune notification disponible.
        </div>
    @endforelse
</div>

        </div>
    </div>
</div>

            </div>
        </div>
    </div>
</nav>
