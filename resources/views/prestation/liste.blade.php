<x-app-layout title="Prestations disponibles" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Prestations disponibles
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
            <li>Prestations disponibles</li>
          </ul> -->
          
</div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif

        @if($prestations && $prestations->count())
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">
            @foreach($prestations as $prestation)
                <div class="card p-4 sm:p-5 bg-white dark:bg-navy-700 rounded-lg shadow-sm">
                    <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">{{ $prestation->titre }}</h3>

                    {{-- Affiche le nom de l'entreprise ou du membre --}}
                    <strong>
                        Proposé par : 
                        {{ $prestation->entreprise->nom ?? 'Non défini' }}
                    </strong>

                    <p>Type: {{ $prestation->prestationtype->titre ?? 'Non défini' }}</p>
                    <p>Prix: {{ $prestation->prix ?? 'Gratuit' }}</p>
                    <p>Durée: {{ $prestation->duree ?? '-' }}</p>

                    @if($prestation->description)
                        <p>{!! $prestation->description !!}</p>
                    @endif
                    
<br />
                <a href="{{ route('prestation.inscrire.form', $prestation->id) }}" 
                  class="btn bg-primary text-white w-full">
                  S'inscrire
                </a>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-slate-400">Aucune prestation disponible pour le moment.</p>
    @endif

            

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>

