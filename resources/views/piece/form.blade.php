<x-app-layout title="Mes Pièces administratives" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne inspiré de document/form.blade.php -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">Mes Pièces administratives</h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">Téléchargez et gérez vos pièces administratives par entreprise</p>
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

                @if(session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('pieces.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Sélection de l'entreprise -->
                    <div class="card shadow-xl mb-6">
                        <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                                </svg>
                                Sélection de l'entreprise
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-navy-200 mt-1">
                                Choisissez l'entreprise pour laquelle vous souhaitez télécharger des pièces
                            </p>
                        </div>
                        <div class="card-body p-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                    Entreprise <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                                        </svg>
                                    </div>
                                    <select name="entreprise_id" required
                                            class="form-select w-full pl-10 rounded-lg border border-slate-300 bg-white px-3 py-2 appearance-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20">
                                        <option value="">Choisir une entreprise</option>
                                        @foreach ($entreprises as $entreprise)
                                            <option value="{{ $entreprise->id }}" {{ old('entreprise_id', $entreprise->entreprise_id ?? '') == $entreprise->id ? 'selected' : '' }}>
                                                {{ $entreprise->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('entreprise_id')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pièces à télécharger -->
                    <div class="card shadow-xl mb-6">
                        <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Pièces à télécharger
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-navy-200 mt-1">
                                Veuillez télécharger les pièces requises pour votre entreprise
                            </p>
                        </div>
                        <div class="card-body p-6">
                            <div class="space-y-6">
                                @foreach ($piecetypes as $piecetype)
                                    @php
                                        $existing = $pieces[$piecetype->id] ?? null;
                                    @endphp
                                    
                                    <div class="border border-slate-200 rounded-lg p-4 hover:bg-slate-50 transition-colors">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-2">
                                                    <div class="h-10 w-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-semibold text-slate-900 dark:text-navy-50">{{ $piecetype->titre }}</h4>
                                                        <p class="text-sm text-slate-500 dark:text-navy-200">
                                                            @if($existing)
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    Déjà téléchargé
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    Requis
                                                                </span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Pièce existante -->
                                                @if ($existing)
                                                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 mb-3">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center">
                                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                <span class="text-sm font-medium text-green-800 dark:text-green-200">Pièce téléchargée</span>
                                                            </div>
                                                            <a href="{{ env('SUPABASE_BUCKET_URL') . '/' . $existing->fichier }}" 
                                                               target="_blank" 
                                                               class="text-sm text-purple-600 hover:text-purple-700 font-medium flex items-center">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                </svg>
                                                                Voir
                                                            </a>
                                                        </div>
                                                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                            Téléchargée le {{ $existing->datedocument->format('d/m/Y H:i') }}
                                                        </p>
                                                    </div>
                                                @endif

                                                <!-- Upload -->
                                                <div class="flex items-center space-x-4">
                                                    <label class="cursor-pointer bg-white px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                                                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                        </svg>
                                                        {{ $existing ? 'Remplacer' : 'Choisir un fichier' }}
                                                        <input type="file" name="piece_{{ $piecetype->id }}" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                    </label>
                                                    <span class="text-sm text-slate-500">PDF, DOC, JPG jusqu'à 10MB</span>
                                                </div>
                                                
                                                <p class="text-xs text-slate-500 mt-2">
                                                    @if($existing)
                                                        Vous pouvez remplacer la pièce existante à tout moment
                                                    @else
                                                        Cette pièce est requise pour la validation de votre entreprise
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card shadow-xl">
                        <div class="card-body p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-slate-600">
                                        Assurez-vous que toutes vos pièces sont claires et lisibles
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Les pièces sont sécurisées et ne sont partagées qu'avec les administrateurs
                                    </p>
                                </div>
                                <div class="flex space-x-3">
                                    <a href="{{ route('dashboard') }}" class="px-6 py-3 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">
                                        Annuler
                                    </a>
                                    <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Enregistrer les pièces
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Liste des pièces existantes -->
                <div class="card shadow-xl mt-6">
                    <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Toutes mes pièces enregistrées
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        @if($pieces->isEmpty())
                            <div class="text-center py-8">
                                <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-2">
                                    Aucune pièce enregistrée
                                </h4>
                                <p class="text-slate-600 dark:text-navy-200">
                                    Commencez par télécharger vos premières pièces administratives
                                </p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-slate-200 dark:border-navy-500">
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700 dark:text-navy-100">Entreprise</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700 dark:text-navy-100">Type de pièce</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700 dark:text-navy-100">Fichier</th>
                                            <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700 dark:text-navy-100">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pieces as $piece)
                                            <tr class="border-b border-slate-100 dark:border-navy-600 hover:bg-slate-50 dark:hover:bg-navy-700/50 transition-colors">
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                                                            </svg>
                                                        </div>
                                                        <span class="font-medium text-slate-800 dark:text-navy-50">{{ $piece->entreprise->nom ?? '—' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                                        {{ $piece->piecetype->titre ?? '—' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    <a href="{{ env('SUPABASE_BUCKET_URL') . '/' . $piece->fichier }}" target="_blank"
                                                       class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Voir
                                                    </a>
                                                </td>
                                                <td class="py-3 px-4 text-sm text-slate-600 dark:text-navy-200">
                                                    {{ \Carbon\Carbon::parse($piece->datedocument)->format('d/m/Y') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    
        </div>
    </main>
</x-app-layout>
