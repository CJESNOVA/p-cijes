<x-app-layout title="Sujets de discussion" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Sujets de discussion
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
          <!-- <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
            <li class="flex items-center space-x-2">
              <a
                class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                href="#"
                >{{ $sujet->titre }}</a
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
            <li>Sujets de discussion</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
    {{-- Sujet --}}
    <div class="card p-4 mb-4">
        <h1 class="font-bold text-xl">{{ $sujet->titre }}</h1>
        <p class="text-gray-600">{{ $sujet->resume }}</p>
        <p class="text-sm text-gray-400">Créé par {{ $sujet->membre->prenom ?? '' }} {{ $sujet->membre->nom ?? '' }}</p>
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

