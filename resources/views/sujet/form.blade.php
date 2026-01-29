<x-app-layout title="{{ isset($sujet) ? 'Modifier ce sujet' : 'Créer un sujet' }}" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        {{ isset($sujet) ? 'Modifier ce sujet' : 'Créer un sujet' }}
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Forum: {{ $forum->titre }}
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

                <!-- Messages d'erreur modernes -->
                @if ($errors->any())
                    <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

        <form action="{{ isset($sujet) ? route('sujet.update', $sujet->id) : route('sujet.store', $forum->id) }}"
              method="POST"
      enctype="multipart/form-data">
            @csrf

            @if(isset($sujet))
                @method('PUT')
            @endif

          <div class="card px-4 pb-4 sm:px-5">
            <div class="max-w-xxl">
              <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:gap-6"
              >

            <div>
                <label for="titre" class="block font-medium">Titre *</label>
                <input type="text" name="titre" id="titre" required maxlength="255"
                       value="{{ old('titre', $sujet->titre ?? '') }}"
                       class="form-input w-full rounded border px-3 py-2" />
            </div>

            <div>
                <label for="resume" class="block font-medium">Résumé</label>
                <textarea name="resume" id="resume" rows="3"
                          class="form-textarea w-full rounded border px-3 py-2">{{ old('resume', $sujet->resume ?? '') }}</textarea>
            </div>

            <div>
                <label for="description" class="block font-medium">Description</label>
                <textarea name="description" id="description" rows="6"
                          class="form-textarea w-full rounded border px-3 py-2">{{ old('description', $sujet->description ?? '') }}</textarea>
            </div>

            
                {{-- Upload vignette --}}
                <div>
                    <label for="vignette" class="block font-medium text-gray-700 dark:text-gray-200 mb-2">Logo ou image (facultatif)</label>

                    <label class="inline-flex items-center justify-center cursor-pointer btn bg-slate-100 font-medium text-slate-800 hover:bg-slate-200 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 transition">
                        <input type="file" name="vignette" id="vignette" class="hidden" accept="image/*">
                        <span>Choisir un fichier</span>
                    </label>

                    @if(!empty($sujet?->vignette))
                        <div class="mt-3">
                            <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $sujet->vignette }}"
                                 alt="Vignette actuelle"
                                 class="w-32 h-32 object-cover rounded shadow-md border border-gray-300 dark:border-gray-600">
                            <p class="text-xs text-gray-500 mt-1">Image actuelle</p>
                        </div>
                    @endif
                </div>


            <div class="flex items-center space-x-2">
                <input type="checkbox" id="spotlight" name="spotlight" value="1" {{ old('spotlight', $sujet->spotlight ?? false) ? 'checked' : '' }} />
                <label for="spotlight" class="select-none">Mettre en avant (spotlight)</label>
            </div>

            <div>
                <button type="submit" class="btn bg-primary text-white hover:bg-primary-focus">
                    {{ isset($sujet) ? 'Modifier' : 'Créer' }}
                </button>
                <a href="{{ route('sujet.index', $forum->id) }}" class="btn ml-2">Annuler</a>
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

