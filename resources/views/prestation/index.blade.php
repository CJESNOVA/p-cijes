<x-app-layout title="Mes Prestations" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                            Mes prestations
                        </h1>
                        <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                            Gérez vos services et offres
                        </p>
                    </div>
                </div>
                <a href="{{ route('prestation.create') }}" 
                   class="px-6 py-3 bg-[#4FBE96] text-white rounded-lg hover:bg-[#4FBE96]/90 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Ajouter une prestation
                </a>
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

        
@if($prestations)
              <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6 xl:grid-cols-3">

            @foreach($prestations as $prestation)
                <div class="card p-4 sm:p-5">
                    <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">{{ $prestation->titre }}</h3>
                    <p>Type: {{ $prestation->prestationtype->titre }}</p>
                    <p>Prix: {{ $prestation->prix }} </p>
                    <p>Durée: {{ $prestation->duree }}</p>
                    <p>{!! $prestation->description !!}</p>
                    <div class="mt-4 flex space-x-2">
                    <a href="{{ route('prestation.edit', $prestation->id) }}" class="btn bg-blue-500 text-white">Modifier</a>
                    <form action="{{ route('prestation.destroy', $prestation->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn bg-red-500 text-white">Supprimer</button>
                    </form>
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

