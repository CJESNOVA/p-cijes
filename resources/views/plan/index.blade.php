<x-app-layout title="Plans d'accompagnement" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                            Plans d'accompagnement
                        </h1>
                        <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                            Gérez vos plans d'action personnalisés
                        </p>
                    </div>
                </div>
                <a href="{{ route('plan.create') }}" 
                   class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nouveau Plan
                </a>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

@if($plans)
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2 xl:gap-6">



    @if($plans->isEmpty())
        <p>Aucun plan trouvé.</p>
    @else
                        
                @foreach($plans as $plan)
            <div class="card p-4">
                <h3 class="font-bold">Accompagnement : {{ $plan->accompagnement->membre->nom ?? '' }} {{ $plan->accompagnement->membre->prenom ?? '' }}{{ $plan->accompagnement->entreprise->nom ?? '' }}</h3>
                <p class="text-sm">Objectif : {{ $plan->objectif }}</p>
                <p class="text-sm">Action prioritaire : {{ $plan->actionprioritaire }}</p>
                <p class="text-xs">{{ \Carbon\Carbon::parse($plan->dateplan)->format('d/m/Y') }}</p>

                <div class="mt-4 flex space-x-2">
                    <a href="{{ route('plan.edit', $plan->id) }}" class="btn bg-blue-500 text-white">Modifier</a>
                    <form action="{{ route('plan.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn bg-red-500 text-white">Supprimer</button>
                    </form>
                </div>
            </div>
                    
                @endforeach
        @endif
          </div>
@endif
          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>




