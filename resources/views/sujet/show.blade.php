<x-app-layout title="Sujets de discussion" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Sujet de discussion
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        {{ $sujet->titre }}
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
                {{-- Sujet moderne --}}
                <div class="card bg-white dark:bg-navy-700 rounded-xl shadow-lg p-6 mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-slate-800 dark:text-navy-50 mb-2">{{ $sujet->titre }}</h2>
                            <p class="text-slate-600 dark:text-navy-200 mb-3">{{ $sujet->resume }}</p>
                            <div class="flex items-center text-sm text-slate-500 dark:text-navy-300">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Créé par {{ $sujet->membre->prenom ?? '' }} {{ $sujet->membre->nom ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>


    {{-- Listing des messages --}}
    <div class="space-y-4">
        @forelse($messages as $message)
            <div class="card p-3 {{ $message->spotlight ? 'border-yellow-500 border-2' : '' }}">
                <p class="text-gray-700">{{ $message->contenu }}</p>
                <p class="text-xs text-gray-400 mt-1">Par {{ $message->membre->prenom ?? '' }} {{ $message->membre->nom ?? '' }} • {{ $message->created_at->diffForHumans() }}</p>
            </div>
        @empty
            <p class="text-gray-500">Aucun message pour le moment.</p>
        @endforelse

        {{ $messages->links() }}

    </div>

<br />

        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif

    {{-- Formulaire message --}}
    <div class="card p-4 mb-6">
        <form action="{{ route('sujet.storeMessage', $sujet->id) }}" method="POST">
            @csrf
            <label for="contenu" class="block font-medium mb-2">Écrire un message :</label>
            <textarea name="contenu" id="contenu" rows="3" required
                      class="form-textarea w-full rounded border px-3 py-2 mb-2">{{ old('contenu') }}</textarea>
            <button type="submit" class="btn bg-primary text-white">Envoyer</button>
        </form>
    </div>

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>

