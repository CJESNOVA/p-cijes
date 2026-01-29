<x-app-layout title="{{ $membre ? 'Modifier mon profil' : 'Créer mon profil' }}" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                    @if($membre && $membre->vignette)
                        <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $membre->vignette }}" alt="Photo" class="h-12 w-12 rounded-full object-cover">
                    @else
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        {{ $membre ? 'Modifier mon profil' : 'Créer mon profil' }}
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        @if($membre)
                            Mettez à jour vos informations personnelles
                        @else
                            Complétez votre profil pour accéder à toutes les fonctionnalités
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
                    <div class="alert flex rounded-lg bg-[#4FBE96] px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('membre.storeOrUpdate') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Informations personnelles -->
                    <div class="card shadow-xl mb-6">
                        <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-[#152737]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Informations personnelles
                            </h3>
                        </div>
                        <div class="card-body p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Numéro d'identifiant -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Numéro d'identifiant
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-4 4h8m-8 0h8"></path>
                                            </svg>
                                        </div>
                                        <input
                                            type="text" 
                                            name="numero_identifiant" 
                                            value="{{ old('numero_identifiant', $membre->numero_identifiant ?? 'MBR' . date('Y') . '00000') }}" 
                                            readonly
                                            class="form-input w-full pl-10 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-slate-600 cursor-not-allowed"
                                            placeholder="Numéro d'identifiant"
                                        />
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">Format: MBRYYYYXXXXX (généré automatiquement)</p>
                                </div>

                                <!-- Type de membre -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Type de membre <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <select name="membretype_id" 
                                                class="form-select w-full pl-10 rounded-lg border border-slate-300 bg-white px-3 py-2 appearance-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" 
                                                required>
                                            <option value="">Choisir un type</option>
                                            @foreach ($membretypes as $membretype)
                                                <option value="{{ $membretype->id }}" {{ (old('membretype_id', $membre->membretype_id ?? '') == $membretype->id) ? 'selected' : '' }}>
                                                    {{ $membretype->titre }} @if($membretype->membrecategorie) ({{ $membretype->membrecategorie->titre }})@endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Nom -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Nom <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <input
                                            type="text" 
                                            name="nom" 
                                            value="{{ old('nom', $membre->nom ?? '') }}" 
                                            required
                                            class="form-input w-full pl-10 rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-[#152737] focus:ring-2 focus:ring-[#152737]/20"
                                            placeholder="Votre nom"
                                        />
                                    </div>
                                </div>

                                <!-- Prénom -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Prénom <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <input
                                            type="text" 
                                            name="prenom" 
                                            value="{{ old('prenom', $membre->prenom ?? '') }}" 
                                            required
                                            class="form-input w-full pl-10 rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-[#152737] focus:ring-2 focus:ring-[#152737]/20"
                                            placeholder="Votre prénom"
                                        />
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Email <span class="text-red-500">*</span>
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
                                            value="{{ old('email', $membre->email ?? Auth::user()->email) }}" 
                                            @if($membre && Auth::user()->email === $membre->email)
                                                readonly
                                                class="form-input w-full pl-10 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-slate-600 cursor-not-allowed"
                                            @else
                                                required
                                                class="form-input w-full pl-10 rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-[#152737] focus:ring-2 focus:ring-[#152737]/20"
                                            @endif
                                            placeholder="votre.email@exemple.com"
                                        />
                                        @if($membre && Auth::user()->email === $membre->email)
                                            <p class="mt-1 text-xs text-slate-500">Email associé au compte utilisateur</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Pays -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Pays <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <select name="pays_id" 
                                                class="form-select w-full pl-10 rounded-lg border border-slate-300 bg-white px-3 py-2 appearance-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                                required>
                                            <option value="">Choisir un pays</option>
                                            @foreach ($payss as $pays)
                                                <option value="{{ $pays->id }}" {{ (old('pays_id', $membre->pays_id ?? '') == $pays->id) ? 'selected' : '' }}>
                                                    {{ $pays->calling_code }} ({{ $pays->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Téléphone -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Téléphone <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                        </div>
                                        <input
                                            type="tel" 
                                            name="telephone" 
                                            value="{{ old('telephone', $membre->telephone ?? '') }}" 
                                            required
                                            class="form-input w-full pl-10 rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-[#152737] focus:ring-2 focus:ring-[#152737]/20"
                                            placeholder="+228 90 00 00 00"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Photo de profil -->
                    <div class="card shadow-xl mb-6">
                        <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-[#4FBE96]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Photo de profil
                            </h3>
                        </div>
                        <div class="card-body p-6">
                            <div class="flex items-center space-x-6">
                                <!-- Photo actuelle -->
                                <div class="flex-shrink-0">
                                    @if($membre && $membre->vignette)
                                        <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $membre->vignette }}" 
                                             alt="Photo de profil" 
                                             class="h-24 w-24 rounded-full object-cover border-4 border-slate-200">
                                    @else
                                        <div class="h-24 w-24 rounded-full bg-slate-200 flex items-center justify-center">
                                            <svg class="h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Upload -->
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Changer la photo (facultatif)
                                    </label>
                                    <div class="flex items-center space-x-4">
                                        <label class="cursor-pointer bg-white px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#152737] focus:border-[#152737]">
                                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            Parcourir
                                            <input type="file" name="vignette" class="hidden" accept="image/*">
                                        </label>
                                        <span class="text-sm text-slate-500">PNG, JPG jusqu'à 5MB</span>
                                    </div>
                                    <p class="mt-2 text-xs text-slate-500">Une photo carrée est recommandée pour un meilleur affichage</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations système -->
                    @if($membre && $membre->numero_identifiant)
                    <div class="card shadow-xl mb-6">
                        <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Informations système
                            </h3>
                        </div>
                        <div class="card-body p-6">
                            <div class="bg-slate-50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">ID Membre</p>
                                        <p class="text-lg font-bold text-slate-900">{{ $membre->numero_identifiant }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600">Statut</p>
                                        <p class="text-lg font-bold text-[#4FBE96]">Actif</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="card shadow-xl">
                        <div class="card-body p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-slate-600">
                                        @if($membre)
                                            Les modifications seront enregistrées immédiatement
                                        @else
                                            En créant votre profil, vous pourrez accéder à toutes les fonctionnalités
                                        @endif
                                    </p>
                                </div>
                                <div class="flex space-x-3">
                                    <a href="{{ route('dashboard') }}" class="px-6 py-3 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">
                                        Annuler
                                    </a>
                                    <button type="submit" class="px-6 py-3 bg-[#152737] text-white rounded-lg hover:bg-[#152737]/90 transition-colors flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ $membre ? 'Mettre à jour' : 'Créer mon profil' }}
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
