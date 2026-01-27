<x-app-layout title="Mes Formations" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                            Mes formations
                        </h1>
                        <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                            Créez et gérez vos programmes de formation
                        </p>
                    </div>
                </div>
                <a href="{{ route('formation.create') }}" 
                   class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nouvelle formation
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

@if($formations)
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">

        @foreach($formations as $f)
            <div class="card p-4">
                <h3 class="font-bold">{{ $f->titre }}</h3>
                <p class="text-sm">Prix: {{ $f->prix ?? '' }}</p>
                <p class="text-sm">{{ $f->formationniveau->titre ?? '' }}</p>
                <p class="text-sm">{{ $f->formationtype->titre ?? '' }}</p>
                <p class="text-xs">Du {{ $f->datedebut }} au {{ $f->datefin }}</p>
                <p class="text-sm">{!! $f->description ?? '' !!}</p>

                <div class="mt-4 flex space-x-2">
                    <a href="{{ route('formation.edit', $f->id) }}" class="btn bg-blue-500 text-white">Modifier</a>
                    <a href="{{ route('formation.participants', $f->id) }}" class="btn bg-green-500 text-white">Participants</a>
                    <form action="{{ route('formation.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
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