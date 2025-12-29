<x-app-layout title="Discussion avec {{ $conversation->membre1->id === $membre->id ? $conversation->membre2->prenom : $conversation->membre1->prenom }}" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Discussion avec {{ $conversation->membre1->id === $membre->id ? $conversation->membre2->prenom : $conversation->membre1->prenom }}
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
            <li>Discussion avec {{ $conversation->membre1->id === $membre->id ? $conversation->membre2->prenom : $conversation->membre1->prenom }}</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
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

