<x-app-layout title="{{ $espace->titre }}"  is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Espaces disponibles
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
          <!-- <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
            <li class="flex items-center space-x-2">
              <a
                class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                href="#"
                >Forms</a
              >
              <svg
                x-ignore
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </li>
            <li>Espaces disponibles</li>
          </ul> -->
      
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                <div class="card p-4 lg:p-6">
                    <!-- Author -->

                    <!-- Blog Post -->
                    <div class="mt-6 font-inter text-base text-slate-600 dark:text-navy-200">
                        <h1 class="text-xl font-medium text-slate-900 dark:text-navy-50 lg:text-2xl">
                            {{ $espace->titre }}
                        </h1>
                        @if($espace && $espace->vignette)
                        <img class="mt-5 h-80 w-full rounded-lg object-cover object-center"
                            src="{{ env('SUPABASE_BUCKET_URL') . '/' . '' . $espace->vignette }}" alt="{{ $espace->titre }}" />
                        <p class="mt-1 text-center text-xs-plus text-slate-400 dark:text-navy-300">
                            <span> {{ $espace->espacetype->titre ?? '-' }} </span>
                        </p>
                        @endif
                        <br />
                        <h3 class="mt-1">
                            {{ $espace->resume }}
                        </h3>
                        <h3 class="mt-1">
                            Prix : {{ $espace->prix }}
                        </h3>
                        <p>
                            {!! $espace->description !!}
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

                        <br />
                        <a href="{{ route('espace.reserver.form', $espace->id) }}"
                   class="btn mt-4 rounded-full bg-primary font-medium text-white hover:bg-primary-focus hover:shadow-lg hover:shadow-primary/50 focus:bg-primary-focus focus:shadow-lg focus:shadow-primary/50 active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:hover:shadow-accent/50 dark:focus:bg-accent-focus dark:focus:shadow-accent/50 dark:active:bg-accent/90">
                    Réserver
                </a>
                        
                    </div>

                </div>

            </div>
            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>