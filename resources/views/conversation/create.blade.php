<x-app-layout title="Démarrer une nouvelle conversation" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            💬 Démarrer une nouvelle conversation
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
          <!-- <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
            <li class="flex items-center space-x-2">
              <a
                class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                href="#"
                >💬 Démarrer une nouvelle conversation</a
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
            <li>💬 Démarrer une nouvelle conversation</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif
        <form action="{{ route('conversation.store') }}" method="POST" id="conversationForm">
    @csrf
    <input type="hidden" name="membre_id" id="selected_membre_id">

    <h2 class="text-lg font-semibold text-slate-800 dark:text-navy-100 mb-4 text-center">
        💬 Choisissez un membre pour commencer la conversation
    </h2>

    @if($membres->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 gap-5">
            @foreach($membres as $m)
                <div 
                    class="cursor-pointer group bg-white dark:bg-navy-700 rounded-2xl shadow hover:shadow-lg transition relative overflow-hidden"
                    onclick="startConversation('{{ $m->id }}')"
                >
                    {{-- Image du membre --}}
                    <div class="relative h-40 w-full bg-slate-200 dark:bg-navy-600">
                        @if($m->vignette)
                            <img src="{{ env('APP_URL') . 'storage/' . $m->vignette }}"
                                 alt="{{ $m->prenom }} {{ $m->nom }}"
                                 class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="flex items-center justify-center h-full text-slate-400 text-4xl">
                                <i class="fas fa-user-circle"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Infos membre --}}
                    <div class="p-4 text-center">
                        <h3 class="font-semibold text-slate-800 dark:text-navy-100">
                            {{ $m->prenom }} {{ $m->nom }}
                        </h3>

                        @if(!empty($m->fonction))
                            <p class="text-sm text-slate-500 dark:text-navy-300">
                                {{ $m->fonction }}
                            </p>
                        @endif

                        {{-- Roles --}}
                        @if(!empty($m->roles))
                            <div class="mt-2 flex flex-wrap justify-center gap-1">
                                @foreach($m->roles as $role)
                                    <span class="text-xs px-2 py-1 rounded-full bg-primary/20 text-primary font-medium">
                                        {{ ucfirst($role) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Overlay au survol --}}
                    <div class="absolute inset-0 bg-primary/70 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                        <span class="text-white font-medium">💬 Commencer</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="mt-4 text-sm text-slate-500 dark:text-navy-300 text-center">
            Aucun autre membre disponible pour le moment.
        </p>
    @endif
</form>

<script>
function startConversation(membreId) {
    document.getElementById('selected_membre_id').value = membreId;
    document.getElementById('conversationForm').submit();
}
</script>

          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>

