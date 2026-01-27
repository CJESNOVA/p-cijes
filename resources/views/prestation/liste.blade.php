<x-app-layout title="Prestations disponibles" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Prestations disponibles
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Découvrez nos services et formations
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

