<x-app-layout title="Ajouter un parrain" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">Ajouter un parrain</h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">Invitez quelqu'un à devenir votre parrain</p>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                <!-- Messages -->
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
                <form action="{{ route('parrainage.store') }}" method="POST">
                    @csrf

                    <!-- Informations du parrain -->
                    <div class="card shadow-xl mb-6">
                        <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Informations du parrain
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-navy-200 mt-1">
                                Entrez l'email de la personne que vous souhaitez inviter comme parrain
                            </p>
                        </div>
                        <div class="card-body p-6">
                            <div class="max-w-2xl mx-auto">
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                                        Email du parrain <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <input
                                            type="email" 
                                            name="email_parrain" 
                                            id="email_parrain" 
                                            required
                                            class="form-input w-full pl-10 rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                            placeholder="email@exemple.com"
                                        />
                                    </div>
                                    <p class="mt-2 text-xs text-slate-500">
                                        Le parrain recevra une invitation par email pour accepter la relation de parrainage
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations sur le parrainage -->
                    <div class="card shadow-xl mb-6">
                        <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Comment ça fonctionne ?
                            </h3>
                        </div>
                        <div class="card-body p-6">
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 h-8 w-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                        <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">1</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-slate-900 dark:text-navy-50">Invitation envoyée</h4>
                                        <p class="text-sm text-slate-600 dark:text-navy-200">
                                            Le parrain recevra un email avec votre invitation
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 h-8 w-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                        <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">2</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-slate-900 dark:text-navy-50">Acceptation</h4>
                                        <p class="text-sm text-slate-600 dark:text-navy-200">
                                            Le parrain doit accepter l'invitation pour activer la relation
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 h-8 w-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                        <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">3</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-slate-900 dark:text-navy-50">Relation active</h4>
                                        <p class="text-sm text-slate-600 dark:text-navy-200">
                                            Une fois acceptée, la relation de parrainage est active
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card shadow-xl">
                        <div class="card-body p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-slate-600">
                                        Assurez-vous que la personne que vous invitez est bien d'accord pour devenir votre parrain
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Une seule invitation par email est envoyée
                                    </p>
                                </div>
                                <div class="flex space-x-3">
                                    <a href="{{ route('parrainage.index') }}" class="px-6 py-3 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">
                                        Annuler
                                    </a>
                                    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        Envoyer l'invitation
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