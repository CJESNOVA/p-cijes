<x-app-layout title="Événements à venir" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Événements à venir
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Découvrez et participez à nos événements
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
          

@if($evenements)
              <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">

      @foreach($evenements as $evenement)
      <div class="card">
                <div class="p-2.5">
                    @if($evenement && $evenement->vignette)
                    <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $evenement->vignette }}" class="h-48 w-full rounded-lg object-cover object-center"
                        alt="{{ $evenement->titre }}" />
                    @endif
                </div>
                <div class="flex grow flex-col px-4 pb-5 pt-1 text-center sm:px-5">
                    <div><a class="text-xs-plus text-info" href="#">{{ $evenement->evenementtype->titre ?? '' }}</a></div>
                    <div class="mt-1">
                        <a href="{{ route('evenement.show', $evenement->id) }}"
                            class="text-lg font-medium text-slate-700 hover:text-primary focus:text-primary dark:text-navy-100 dark:hover:text-accent-light dark:focus:text-accent-light">{{ $evenement->titre }}</a>
                    </div>
                    <div class="my-2 flex items-center space-x-3 text-xs">
                        <div class="h-px flex-1 bg-slate-200 dark:bg-navy-500"></div>
                        <p>{{ \Carbon\Carbon::parse($evenement->dateevenement)->format('d/m/Y') }}</p>
                        <div class="h-px flex-1 bg-slate-200 dark:bg-navy-500"></div>
                    </div>
                    <p class="my-2 grow text-left line-clamp-3">
                        {{ $evenement->resume }}
                    </p>
                    <p class="my-2 grow text-left line-clamp-3">
                        Prix : {{ $evenement->prix }}
                    </p>
                    <div>
                        <a href="{{ route('evenement.show', $evenement->id) }}"
                            class="btn mt-4 rounded-full bg-info font-medium text-white hover:bg-info-focus hover:shadow-lg hover:shadow-info/50 focus:bg-info-focus focus:shadow-lg focus:shadow-info/50 active:bg-info-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:hover:shadow-accent/50 dark:focus:bg-accent-focus dark:focus:shadow-accent/50 dark:active:bg-accent/90">
                            Voir détails
                        </a>
                        
                        @if(in_array($evenement->id, $inscriptions ?? []))
                            <!-- Déjà inscrit -->
                            <span class="btn mt-4 rounded-full bg-green-500 font-medium text-white cursor-not-allowed opacity-75">
                                ✅ Déjà inscrit
                            </span>
                        @else
                            <!-- Bouton d'inscription -->
                            <a href="{{ route('evenement.inscrire.form', $evenement->id) }}" 
                              class="btn mt-4 rounded-full bg-primary font-medium text-white hover:bg-primary-focus hover:shadow-lg hover:shadow-primary/50 focus:bg-primary-focus focus:shadow-lg focus:shadow-primary/50 active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:hover:shadow-accent/50 dark:focus:bg-accent-focus dark:focus:shadow-accent/50 dark:active:bg-accent/90">
                              S'inscrire
                            </a>
                        @endif
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