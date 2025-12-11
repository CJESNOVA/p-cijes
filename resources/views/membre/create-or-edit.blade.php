<x-app-layout title="{{ $membre ? 'Modifier mon profil' : 'Créer mon profil' }}" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            {{ $membre ? 'Modifier mon profil' : 'Créer mon profil' }}
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
          <!-- <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
            <li class="flex items-center space-x-2">
              <a
                class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                href="#"
                >Forms</a
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
            <li>{{ $membre ? 'Modifier mon profil' : 'Créer mon profil' }}</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

    <form action="{{ route('membre.storeOrUpdate') }}" method="POST" enctype="multipart/form-data">
        @csrf

    @if(session('error'))
        <div class="mb-4 p-3 rounded bg-red-100 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
    @endif


          <!-- Input Validation -->
          <div class="card px-4 pb-4 sm:px-5">
            <div class="max-w-xxl">
              <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6"
              >
                <div>
                  <label class="block">
                    <span>Nom </span>
                  <input
                    class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                    placeholder="Nom"
                    type="text" name="nom" value="{{ old('nom', $membre->nom ?? '') }}" required
                  />
                </label>
                </div>

                <div>
                  <label class="block">
                    <span>Prénom </span>
                  <input
                    class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                    placeholder="Prénom"
                    type="text" name="prenom" value="{{ old('prenom', $membre->prenom ?? '') }}" required
                  />
                  </label>
                </div>

                <div>
                  <label class="block">
                    <span>Email </span>
                  <input
                    class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                    placeholder="Email"
                    type="email" name="email" value="{{ old('email', $membre->email ?? '') }}" required
                  />
                  </label>
                </div>

                <div>
                  <label class="block">
                    <span>Type de membre</span>
            <select name="membretype_id" 
      class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent" required>
    <option value="0">Choisir un type</option>
                @foreach ($membretypes as $membretype)
                    <option value="{{ $membretype->id }}" {{ (old('membretype_id', $membre->membretype_id ?? '') == $membretype->id) ? 'selected' : '' }}>
                        {{ $membretype->titre }}
                    </option>
                @endforeach
            </select>
                  </label>
                </div>

                <div>
                  <label class="block">
                    <span>Pays</span>
            <select name="pays_id" 
    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent"
    required>
    <option value="">Choisir un pays</option>
    @foreach ($payss as $pays)
        <option value="{{ $pays->id }}" {{ (old('pays_id', $membre->pays_id ?? '') == $pays->id) ? 'selected' : '' }}>
            {{ $pays->calling_code }} ({{ $pays->name }})
        </option>
    @endforeach
</select>
                  </label>
                </div>

                <div>
                  <label class="block">
                    <span>Téléphone</span>
                  <input
                    class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                    placeholder="Téléphone"
                    type="number" name="telephone" value="{{ old('telephone', $membre->telephone ?? '') }}" required
                  />
                  </label>
                </div>

                
            <div class="max-w-xxl">
                    <span>Photo (facultatif)</span>
              <div class="mt-1">
                <label
    class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
  >
    <input 
    class="form-control"
      type="file" name="vignette"
    />
    
  </label>
  

              </div>
            </div>


                <div>
                    
                </div>   
                <div>
            @if($membre && $membre->vignette)
                <img src="{{ env('APP_URL') . 'storage/' . $membre->vignette }}" alt="Vignette" width="100">
            @endif
                  
                </div>   
                <div>
                  <button type="submit"
    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90"
  >
    Enregistrer
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
