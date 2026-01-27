<x-app-layout title="{{ isset($entreprise) ? 'Modifier' : 'Créer' }} une entreprise" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-lg">
                    @if($entreprise && $entreprise->vignette)
                        <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $entreprise->vignette }}" alt="Logo" class="h-12 w-12 rounded-lg object-cover">
                    @else
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                        </svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        {{ isset($entreprise) ? 'Modifier' : 'Créer' }} une entreprise
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        @if($entreprise)
                            Mettez à jour les informations de votre entreprise
                        @else
                            Enregistrez votre entreprise pour accéder à toutes les fonctionnalités
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                <!-- Messages -->
                @if(session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert flex rounded-lg bg-green-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

    @if (session('info'))
        <div class="alert flex rounded-lg bg-info px-4 py-4 text-white sm:px-5">{{ session('info') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert flex rounded-lg bg-warning px-4 py-4 text-white sm:px-5">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

                <form action="{{ $entreprise ? route('entreprise.update', $entreprise->id) : route('entreprise.store') }}"
                      method="{{ $entreprise ? 'POST' : 'POST' }}"
                      enctype="multipart/form-data">
                    @csrf
                    @if($entreprise)
                        @method('PUT')
                    @endif

                    <!-- Informations de l'entreprise -->
                    <div class="card shadow-xl mb-6">
                        <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                                </svg>
                                Informations de l'entreprise
                            </h3>
                        </div>
                        <div class="card-body p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nom de l'entreprise -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Nom de l'entreprise <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                                            </svg>
                                        </div>
                                        <input
                                            type="text" 
                                            name="nom" 
                                            value="{{ old('nom', $entreprise->nom ?? '') }}" 
                                            required
                                            class="form-input w-full pl-10 rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                            placeholder="Nom de l'entreprise"
                                        />
                                    </div>
                                    @error('nom')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email de l'entreprise -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Email de l'entreprise <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <input
                                            type="email" 
                                            name="email" 
                                            value="{{ old('email', $entreprise->email ?? '') }}" 
                                            required
                                            class="form-input w-full pl-10 rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                            placeholder="entreprise@exemple.com"
                                        />
                                    </div>
                                    @error('email')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>


                <div>
                  <label class="block">
                    <span>Secteur d'activité</span>
            <select name="secteur_id" 
      class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent" required>
    <option value="">-- Choisir --</option>
                    @foreach ($secteurs as $secteur)
                        <option value="{{ $secteur->id }}" {{ old('secteur_id', $entreprise->secteur_id ?? '') == $secteur->id ? 'selected' : '' }}>
                            {{ $secteur->titre }}
                        </option>
                    @endforeach
            </select>
                  </label>
                </div>

                <div>
                  <label class="block">
                    <span>Type d'entreprise</span>
            <select name="entreprisetype_id" 
      class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent" required>
    <option value="">-- Choisir --</option>
                    @foreach ($entreprisetypes as $type)
                        <option value="{{ $type->id }}" {{ old('entreprisetype_id', $entreprise->entreprisetype_id ?? '') == $type->id ? 'selected' : '' }}>
                            {{ $type->titre }}
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
        <option value="{{ $pays->id }}" {{ (old('pays_id', $entreprise->pays_id ?? '') == $pays->id) ? 'selected' : '' }}>
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
                    type="number" name="telephone" value="{{ old('telephone', $entreprise->telephone ?? '') }}" required
                  />
                  </label>
                </div>

                <div>
                  <label class="block">
                    <span>Année de création</span>
                  <input
                    class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                    placeholder="Année de création"
                    type="number" name="annee_creation" value="{{ old('annee_creation', $entreprise->annee_creation ?? '') }}" min="1900" max="{{ date('Y') }}"
                  />
                  </label>
                </div>

                <div>
                  <label class="block">
                    <span>Statut CJES</span>
                    <div class="mt-2">
                        @php
                            $anneeMin = date('Y') - 10;
                            $peutEtreMembre = !$entreprise || ($entreprise->annee_creation && $entreprise->annee_creation >= $anneeMin);
                        @endphp
                        
                        @if($peutEtreMembre)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="est_membre_cijes" value="1" {{ old('est_membre_cijes', $entreprise->est_membre_cijes ?? 0) ? 'checked' : '' }} class="form-checkbox is-outline size-5 rounded-sm border-slate-400/70 bg-slate-100 before:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-500 dark:bg-navy-900 dark:before:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent">
                                <span class="ml-2">Membre CJES</span>
                            </label>
                            <small class="block text-xs text-slate-400 mt-1">
                                @if($entreprise && $entreprise->annee_creation)
                                    Cette entreprise peut être membre CJES (créée en {{ $entreprise->annee_creation }})
                                @else
                                    Les entreprises de moins de 10 ans peuvent être membres CJES
                                @endif
                            </small>
                        @else
                            <div class="inline-flex items-center text-slate-500">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span>Non éligible au membre CJES</span>
                            </div>
                            <small class="block text-xs text-red-400 mt-1">
                                Cette entreprise ne peut pas être membre CJES (plus de 10 ans)
                            </small>
                        @endif
                    </div>
                  </label>
                </div>

                <div>
                  <label class="block">
                    <span>Votre fonction dans l'entreprise</span>
                  <input
                    class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                    placeholder="Ex: Fondateur, Directrice, Responsable marketing"
                    type="text" name="fonction" value="{{ old('fonction', $entreprisemembre->fonction ?? '') }}" required
                  />
                  </label>
                </div>

                <div>
                  <label class="block">
                    <span>Votre bio / parcours</span>
    <textarea
            class="form-textarea w-full rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
            placeholder="Décrivez brièvement votre parcours, motivation ou expérience"
            name="bio"
            rows="4"
        >{{ old('bio', $entreprisemembre->bio ?? '') }}</textarea>
                  </label>
                </div>
                
                <div>
                  <label class="block">
                    <span>Adresse </span>
                  <textarea
                    class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                    placeholder="Adresse"
                    type="text" name="adresse" rows="4"
                  >{{ old('adresse', $entreprise->adresse ?? '') }}</textarea>
                </label>
                </div>

                <div>
                  <label class="block">
                    <span>Description </span>
                <textarea name="description" rows="4" class="form-textarea w-full rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
    >{{ old('description', $entreprise->description ?? '') }}</textarea>
                  </label>
                </div>


            <div class="max-w-xxl">
                    <span>Logo ou image (facultatif)</span>
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
            @if($entreprise && $entreprise->vignette)
                <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $entreprise->vignette }}" alt="Vignette" width="100">
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