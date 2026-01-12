<x-app-layout title="Choisir une entreprise" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Test de qualification
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
                @if(session('success'))
                    <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert flex rounded-lg bg-danger px-4 py-4 text-white sm:px-5">{{ session('error') }}</div>
                @endif

                <div class="card">
                    <div class="card-title">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-100">
                            Choisir une entreprise pour le test de qualification
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-navy-300 mt-1">
                            Sélectionnez l'entreprise que vous souhaitez évaluer avec le test de qualification.
                        </p>
                    </div>
                    
                    <div class="card-body">
                        @if($entreprises->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($entreprises as $entreprise)
                                    <div class="card hover:shadow-lg transition-shadow cursor-pointer">
                                        <div class="card-body">
                                            <div class="flex items-center mb-4">
                                                @if($entreprise->vignette)
                                                    <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $entreprise->vignette }}" 
                                                         alt="{{ $entreprise->nom }}" 
                                                         class="w-16 h-16 rounded-full object-cover mr-4">
                                                @else
                                                    <div class="w-16 h-16 rounded-full bg-slate-200 dark:bg-navy-600 flex items-center justify-center mr-4">
                                                        <i class="fas fa-building text-slate-400 dark:text-navy-400 text-xl"></i>
                                                    </div>
                                                @endif
                                                
                                                <div>
                                                    <h4 class="font-semibold text-slate-800 dark:text-navy-100">
                                                        {{ $entreprise->nom }}
                                                    </h4>
                                                    <p class="text-sm text-slate-600 dark:text-navy-300">
                                                        {{ $entreprise->secteur->titre ?? 'Non spécifié' }}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <a href="{{ route('diagnosticentreprisequalification.showForm', $entreprise->id) }}" 
                                               class="btn bg-warning text-white hover:bg-warning-focus w-full">
                                                <i class="fas fa-clipboard-check mr-2"></i>
                                                Commencer le test
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-building text-4xl text-slate-400 mb-4"></i>
                                <h3 class="text-lg font-medium text-slate-700 dark:text-navy-200 mb-2">
                                    Aucune entreprise trouvée
                                </h3>
                                <p class="text-slate-600 dark:text-navy-400 mb-6">
                                    Vous devez d'abord créer une entreprise avant de pouvoir effectuer un test de qualification.
                                </p>
                                <a href="{{ route('entreprise.create') }}" 
                                   class="btn bg-primary text-white hover:bg-primary-focus">
                                    <i class="fas fa-plus mr-2"></i>
                                    Créer une entreprise
                                </a>
                            </div>
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
