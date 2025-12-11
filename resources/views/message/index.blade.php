<x-app-layout title="Mes conversations" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            💬 Mes conversations
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
          <!-- <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
            <li class="flex items-center space-x-2">
              <a
                class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                href="#"
                >💬 Mes conversations</a
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
            <li>💬 Mes conversations</li>
          </ul> -->
          
          <a href="{{ route('conversation.create') }}"
   class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg shadow hover:bg-primary-focus transition">
   ➕ Nouvelle conversation
</a>

        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif

@if($conversations->count())
    <div class="space-y-3">
        @foreach($conversations as $conv)
            @php
                $autre = $conv->membre1->id === $membre->id ? $conv->membre2 : $conv->membre1;
                $lastMessage = $conv->messages->last();
                // Compte des messages non lus envoyés par l'autre membre
                $nonLus = $conv->messages()
                    ->where('membre_id', $autre->id)
                    ->where('lu', 0)
                    ->count();
            @endphp

            <a href="{{ route('message.show', $conv->id) }}"
               class="flex items-center gap-3 bg-white dark:bg-navy-700 p-4 rounded-xl shadow-sm hover:shadow-md transition duration-200 border border-transparent hover:border-primary/40">

                {{-- Avatar du correspondant --}}
                <div class="relative flex-shrink-0">
                    <img src="{{ env('APP_URL') . 'storage/' . $autre->vignette ?? asset('images/200x200.png') }}"
                         alt="avatar"
                         class="w-12 h-12 rounded-full border border-slate-200 dark:border-navy-600 object-cover">
                    <span class="absolute bottom-0 right-0 block w-3 h-3 rounded-full 
                        {{ $autre->en_ligne ?? false ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                </div>

                {{-- Détails conversation --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-slate-800 dark:text-navy-100 truncate">
                            {{ $autre->prenom }} {{ $autre->nom }}
                        </p>
                        @if($lastMessage)
                            <p class="text-xs text-slate-400 dark:text-navy-300 whitespace-nowrap ml-2">
                                {{ $lastMessage->created_at->diffForHumans() }}
                            </p>
                        @endif
                    </div>

                    <p class="text-sm text-slate-500 dark:text-navy-300 truncate mt-1">
                        💬 {{ $lastMessage->contenu ?? 'Aucun message encore' }}
                    </p>
                </div>

                {{-- Badge de messages non lus --}}
                @if($nonLus > 0)
                    <div class="ml-3 flex-shrink-0">
                        <span class="px-2 py-1 text-xs font-semibold bg-primary text-white rounded-full shadow-sm">
                            {{ $nonLus }} nouveau{{ $nonLus > 1 ? 'x' : '' }}
                        </span>
                    </div>
                @endif
            </a>
        @endforeach
    </div>
@else
    <div class="text-center py-10 text-slate-500 dark:text-navy-300">
        <i class="fas fa-comments text-3xl mb-2 opacity-50"></i>
        <p>Aucune conversation pour le moment.</p>
    </div>
@endif


          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>

