<x-app-layout title="Évaluer" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-1DA8BB to-1DA8BB flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">Évaluer</h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">Donnez votre évaluation sur la prestation ou l'expert</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
<form action="{{ route('evaluation.store', $expert->id) }}" method="POST">
    @csrf

    {{-- Message succès --}}
    @if (session('success'))
        <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card px-4 pb-4 sm:px-5">
        <div class="max-w-xxl">
            <div class="mt-5 grid grid-cols-1 gap-6">

                {{-- Sélection de la note --}}
                <div class="mt-4">
                    <label class="block font-medium text-slate-700 dark:text-navy-100 mb-2">
                        Votre note :
                    </label>

                    <div class="flex space-x-2 mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            <input 
                                type="radio" 
                                name="note" 
                                id="note{{ $i }}" 
                                value="{{ $i }}" 
                                class="hidden" 
                                {{ isset($myEvaluation) && $myEvaluation->note == $i ? 'checked' : '' }} 
                                required
                            >
                            <label for="note{{ $i }}" class="cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-8 h-8 transition-colors duration-200 
                                    {{ isset($myEvaluation) && $myEvaluation->note >= $i ? 'text-yellow-400' : 'text-gray-400' }}"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.868 
                                            1.48 8.241L12 18.896l-7.416 4.519 
                                            1.48-8.241L0 9.306l8.332-1.151z"/>
                                </svg>
                            </label>
                        @endfor
                    </div>
                </div>

                {{-- Script pour changement dynamique de couleur --}}
                <script>
                    document.querySelectorAll('input[name="note"]').forEach(radio => {
                        radio.addEventListener('change', () => {
                            const value = parseInt(radio.value);
                            document.querySelectorAll('label[for^="note"]').forEach((label, index) => {
                                const star = label.querySelector('svg');
                                if (index < value) {
                                    star.classList.add('text-yellow-400');
                                    star.classList.remove('text-gray-400');
                                } else {
                                    star.classList.remove('text-yellow-400');
                                    star.classList.add('text-gray-400');
                                }
                            });
                        });
                    });
                </script>

                {{-- Commentaire --}}
                <div class="mt-4">
                    <label class="block font-medium text-slate-700 dark:text-navy-100 mb-2">
                        Commentaire (facultatif)
                    </label>
                    <textarea name="commentaire"
                        class="form-textarea w-full rounded-lg border border-slate-300 bg-transparent p-2.5
                        placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary
                        dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                        rows="3">{{ $myEvaluation->commentaire ?? '' }}</textarea>
                </div>

                {{-- Bouton --}}
                <div class="mt-6">
                    <button type="submit" class="btn bg-primary text-white rounded-lg hover:bg-primary-focus transition">
                        <i class="fas fa-paper-plane mr-1"></i> Envoyer l’évaluation
                    </button>
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
