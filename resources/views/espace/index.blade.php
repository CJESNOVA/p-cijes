<x-app-layout title="Espaces disponibles" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Espaces disponibles
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Réservez vos espaces de travail et de réunion
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
                <!-- Messages modernes -->
                @if(session('success'))
                    <div class="alert flex rounded-lg bg-green-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
    
@if($espaces)
              <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">

      @foreach($espaces as $espace)
            <div class="card">
                @if($espace && $espace->vignette)
                <div class="p-2.5">
                    <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $espace->vignette }}" class="h-48 w-full rounded-lg object-cover object-center"
                        alt="{{ $espace->titre }}" />
                </div>
                @endif
                <div class="flex grow flex-col px-4 pb-5 pt-1 text-center sm:px-5">
                    <div><a class="text-xs-plus text-info">{{ $espace->espacetype->titre ?? '-' }}</a></div>
                    <div class="mt-1">
                        <a href="#"
                            class="text-lg font-medium text-slate-700 hover:text-primary focus:text-primary dark:text-navy-100 dark:hover:text-accent-light dark:focus:text-accent-light">{{ $espace->titre }}</a>
                    </div>
                    <div class="mt-1">
                        Prix : {{ $espace->prix }}
                    </div>
                    <div class="my-2 flex items-center space-x-3 text-xs">
                        <div class="h-px flex-1 bg-slate-200 dark:bg-navy-500"></div>
                        <p><a class="btn mt-4 rounded-full bg-primary font-medium text-white hover:bg-primary-focus hover:shadow-lg hover:shadow-primary/50 focus:bg-primary-focus focus:shadow-lg focus:shadow-primary/50 active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:hover:shadow-accent/50 dark:focus:bg-accent-focus dark:focus:shadow-accent/50 dark:active:bg-accent/90" href="{{ route('espace.show', $espace->id) }}">Détails</a></p>
                        <div class="h-px flex-1 bg-slate-200 dark:bg-navy-500"></div>
                    </div>
                    <p class="my-2 grow text-left line-clamp-3">
                        {{ $espace->resume }}
                    </p>

                    <strong>Dates réservées :</strong>
        @if($espace->reservationsAVenir->count())
            <ul>
                @foreach($espace->reservationsAVenir as $reservation)
                    <li>
                        {{ \Carbon\Carbon::parse($reservation->datedebut)->format('d/m/Y') }}
                        - 
                        {{ \Carbon\Carbon::parse($reservation->datefin)->format('d/m/Y') }}
                        ({{ $reservation->reservationstatut->titre ?? 'Non défini' }})
                    </li>
                @endforeach
            </ul>
        @else
            <em>Aucune réservation</em>
        @endif

                    <div>
                        <a href="{{ route('espace.reserver.form', $espace->id) }}"
                   class="btn mt-4 rounded-full bg-primary font-medium text-white hover:bg-primary-focus hover:shadow-lg hover:shadow-primary/50 focus:bg-primary-focus focus:shadow-lg focus:shadow-primary/50 active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:hover:shadow-accent/50 dark:focus:bg-accent-focus dark:focus:shadow-accent/50 dark:active:bg-accent/90">
                    Réserver
                </a>
                    </div>
                </div>
            </div>
            
      @endforeach
          </div>
@endif
          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>