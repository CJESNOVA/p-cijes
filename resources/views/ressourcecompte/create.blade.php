<x-app-layout title="Ajouter une ressource ({{ $type->titre }})" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Ajouter une ressource
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Type: {{ $type->titre }}
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

                <!-- Messages modernes -->
                @if (session('success'))
                    <div class="alert flex rounded-lg bg-green-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

        <form method="POST" action="{{ route('ressourcecompte.store') }}" class="space-y-4">
    @csrf

    <div class="card px-4 pb-4 sm:px-5">
        <div class="max-w-xxl">
            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6">

                {{-- ✅ Sélection de l’entreprise (ou membre seul) --}}
                @if($entreprises->isEmpty())
                    <input type="hidden" name="entreprise_id" value="">
                @else
                    <div>
                        <label class="block font-medium mb-1">Entreprise</label>
                        <select name="entreprise_id"
                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 
                                hover:border-slate-400 focus:border-primary dark:border-navy-450 
                                dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                            <option value="">-- Choisir une entreprise --</option>
                            @foreach ($entreprises as $e)
                                <option value="{{ $e->entreprise->id }}" {{ old('entreprise_id') == $e->entreprise->id ? 'selected' : '' }}>
                                    {{ $e->entreprise->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('entreprise_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                {{-- ✅ Solde initial --}}
                <div>
                    <label class="block font-medium mb-1">Solde initial (en FCFA)</label>
                    <input type="number" name="solde" id="solde"
                        class="w-full border rounded-lg px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary"
                        value="{{ old('solde', 0) }}" required>
                    @error('solde')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- ✅ Boutons --}}
                <div class="sm:col-span-2 mt-3">
                    <button type="submit"
                        class="px-4 py-2 bg-[#4FBE96] hover:bg-[#4FBE96]/90 text-white rounded-lg transition">
                        Enregistrer
                    </button>
                    <a href="{{ route('ressourcecompte.index') }}"
                        class="ml-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                        Annuler
                    </a>
                </div>

            </div>
        </div>
    </div>
</form>


          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>
