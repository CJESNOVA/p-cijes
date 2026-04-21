<x-app-layout title="Mes conversations" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                            Mes conversations
                        </h1>
                        <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                            Gérez vos messages et échanges
                        </p>
                    </div>
                </div>
                <a href="{{ route('conversation.create') }}"
                   class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nouvelle conversation
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
                    <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $autre->vignette ?? asset('images/200x200.png') }}"
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

