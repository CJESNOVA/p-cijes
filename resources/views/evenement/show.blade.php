<x-app-layout title="{{ $evenement->titre }}"  is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Événements à venir
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
            <li>Événements à venir</li>
          </ul> -->
      
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                <div class="card p-4 lg:p-6">
                    <!-- Author -->

                    <!-- Blog Post -->
                    <div class="mt-6 font-inter text-base text-slate-600 dark:text-navy-200">
                        <h1 class="text-xl font-medium text-slate-900 dark:text-navy-50 lg:text-2xl">
                            {{ $evenement->titre }}
                        </h1>

    <p class="mt-4 text-sm">📅 Date : {{ \Carbon\Carbon::parse($evenement->dateevenement)->format('d/m/Y') }}</p>

                        @if($evenement && $evenement->vignette)
                        <img class="mt-5 h-80 w-full rounded-lg object-cover object-center"
                            src="{{ env('SUPABASE_BUCKET_URL') . '/' . '' . $evenement->vignette }}" alt="{{ $evenement->titre }}" />
                        <p class="mt-1 text-center text-xs-plus text-slate-400 dark:text-navy-300">
                            <span> {{ $evenement->evenementtype->titre ?? '-' }} </span>
                        </p>
                        @endif
                        <br />
                        <h3 class="mt-1">
                            {{ $evenement->resume }}
                        </h3>
                        <h3 class="mt-1">
                            Prix : {{ $evenement->prix }}
                        </h3>
                        <p>
                            {!! $evenement->description !!}
                        </p>


    @if($dejaInscrit)
        <p class="mt-4 text-green-600 font-semibold">✅ Vous êtes déjà inscrit</p>
        <p>Type d’inscription : {{ $dejaInscrit->evenementinscriptiontype?->titre ?? 'N/A' }}</p>
    @else
        <a href="{{ route('evenement.inscrire.form', $evenement->id) }}" 
                  class="btn mt-4 rounded-full bg-primary font-medium text-white hover:bg-primary-focus hover:shadow-lg hover:shadow-primary/50 focus:bg-primary-focus focus:shadow-lg focus:shadow-primary/50 active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:hover:shadow-accent/50 dark:focus:bg-accent-focus dark:focus:shadow-accent/50 dark:active:bg-accent/90">
                  S'inscrire
                </a>
    @endif
                        
                    </div>

                </div>

            </div>
            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>