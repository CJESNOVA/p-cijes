<x-app-layout title="Mes Documents" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">Mes Documents</h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">Téléchargez et gérez vos documents administratifs</p>
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
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Documents requis -->
                    <div class="card shadow-xl mb-6">
                        <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Documents à télécharger
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-navy-200 mt-1">
                                Veuillez télécharger tous les documents requis pour compléter votre profil
                            </p>
                        </div>
                        <div class="card-body p-6">
                            <div class="space-y-6">
                                @foreach ($documenttypes as $documenttype)
                                    @php
                                        $existing = $documents[$documenttype->id] ?? null;
                                    @endphp
                                    
                                    <div class="border border-slate-200 rounded-lg p-4 hover:bg-slate-50 transition-colors">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-2">
                                                    <div class="h-10 w-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-semibold text-slate-900 dark:text-navy-50">{{ $documenttype->titre }}</h4>
                                                        <p class="text-sm text-slate-500 dark:text-navy-200">
                                                            @if($existing)
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#4FBE96]/20 text-[#4FBE96] dark:bg-[#4FBE96]/30 dark:text-[#4FBE96]">
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
                                                
                                                <!-- Document existant -->
                                                @if ($existing)
                                                    <div class="bg-[#4FBE96]/10 dark:bg-[#4FBE96]/20 rounded-lg p-3 mb-3">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center">
                                                                <svg class="w-4 h-4 text-[#4FBE96] dark:text-[#4FBE96]/80 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                <span class="text-sm font-medium text-[#4FBE96] dark:text-[#4FBE96]/80">Document téléchargé</span>
                                                            </div>
                                                            <a href="{{ env('SUPABASE_BUCKET_URL') . '/' . $existing->fichier }}" 
                                                               target="_blank" 
                                                               class="text-sm text-emerald-600 hover:text-emerald-700 font-medium flex items-center">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                </svg>
                                                                Voir
                                                            </a>
                                                        </div>
                                                        <p class="text-xs text-[#4FBE96] dark:text-[#4FBE96]/80 mt-1">
                                                            Téléchargé le {{ $existing->created_at->format('d/m/Y H:i') }}
                                                        </p>
                                                    </div>
                                                @endif

                                                <!-- Upload -->
                                                <div class="flex items-center space-x-4">
                                                    <label class="cursor-pointer bg-white px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                        </svg>
                                                        {{ $existing ? 'Remplacer' : 'Choisir un fichier' }}
                                                        <input type="file" name="document_{{ $documenttype->id }}" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                    </label>
                                                    <span class="text-sm text-slate-500">PDF, DOC, JPG jusqu'à 10MB</span>
                                                </div>
                                                
                                                <p class="text-xs text-slate-500 mt-2">
                                                    @if($existing)
                                                        Vous pouvez remplacer le document existant à tout moment
                                                    @else
                                                        Ce document est requis pour la validation de votre profil
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
                                        Assurez-vous que tous vos documents sont clairs et lisibles
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Les documents sont sécurisés et ne sont partagés qu'avec les administrateurs
                                    </p>
                                </div>
                                <div class="flex space-x-3">
                                    <a href="{{ route('dashboard') }}" class="px-6 py-3 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">
                                        Annuler
                                    </a>
                                    <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Enregistrer les documents
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
