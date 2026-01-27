<x-app-layout title="Discussion avec {{ $conversation->membre1->id === $membre->id ? $conversation->membre2->prenom : $conversation->membre1->prenom }}" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Discussion
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Avec {{ $conversation->membre1->id === $membre->id ? $conversation->membre2->prenom : $conversation->membre1->prenom }}
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

<div class="bg-white dark:bg-navy-700 p-4 rounded-2xl shadow-lg max-h-[70vh] overflow-y-auto space-y-4">
    @foreach($conversation->messages as $msg)
        @php
            $isMine = $msg->membre_id == $membre->id;
        @endphp

        <div class="flex items-end {{ $isMine ? 'justify-end' : 'justify-start' }}">
            {{-- Avatar --}}
            @unless($isMine)
                <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $msg->membre->vignette ?? asset('images/200x200.png') }}"
                     alt="avatar"
                     class="w-10 h-10 rounded-full border border-slate-300 dark:border-navy-600 mr-3">
            @endunless

            {{-- Message Bubble --}}
            <div class="max-w-xs md:max-w-md lg:max-w-lg">
                <div class="p-3 rounded-2xl shadow-sm 
                    {{ $isMine 
                        ? 'bg-primary text-white rounded-br-none' 
                        : 'bg-slate-200 dark:bg-navy-600 text-slate-800 dark:text-navy-50 rounded-bl-none' }}">
                    <p class="text-sm leading-relaxed">{{ $msg->contenu }}</p>
                </div>
                <p class="text-xs mt-1 text-slate-400 dark:text-navy-300 {{ $isMine ? 'text-right' : '' }}">
                    {{ \Carbon\Carbon::parse($msg->created_at)->translatedFormat('d M Y H:i') }}
                </p>
            </div>

            {{-- Avatar for own message (optional) --}}
            @if($isMine)
                <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $membre->vignette ?? asset('images/200x200.png') }}"
                     alt="avatar"
                     class="w-10 h-10 rounded-full border border-slate-300 dark:border-navy-600 ml-3">
            @endif
        </div>
    @endforeach
</div>

{{-- Zone de saisie du message --}}
<form action="{{ route('message.store', $conversation->id) }}" method="POST" 
      class="mt-4 flex items-center gap-2 bg-white dark:bg-navy-700 p-3 rounded-xl shadow-md">
    @csrf
    <input type="text" name="contenu" placeholder="Écrivez votre message..."
           class="form-input w-full rounded-full border border-slate-300 bg-transparent px-4 py-2 
                  placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary 
                  dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent text-sm">
    <button type="submit"
            class="flex items-center justify-center w-10 h-10 rounded-full bg-primary hover:bg-primary-focus text-white transition">
        <i class="fas fa-paper-plane"></i>
    </button>
</form>

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>

