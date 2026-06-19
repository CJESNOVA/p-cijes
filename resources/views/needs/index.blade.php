<x-app-layout title="Besoins" is-sidebar-open="true" is-header-blur="true">

    <main class="main-content w-full px-[var(--margin-x)] pb-8">

        <!-- Header moderne -->
        <div class="mb-10">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">Besoins</h1>
                        <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">Découvrez tous les besoins disponibles</p>
                    </div>
                </div>
                <a href="{{ route('needs.create') }}"
                   class="btn rounded-lg bg-primary px-6 py-2.5 font-medium text-white hover:bg-primary-focus dark:bg-accent dark:hover:bg-accent-focus">
                    <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nouveau besoin
                </a>
            </div>
        </div>

        <!-- Messages d'alerte -->
        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5 mb-6">
                <svg class="h-5 w-5 flex-none" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert flex rounded-lg bg-error px-4 py-4 text-white sm:px-5 mb-6">
                <svg class="h-5 w-5 flex-none" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">{{ session('error') }}</div>
            </div>
        @endif

        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 lg:col-span-8">
                <div class="grid grid-cols-1 gap-6">
                    @if($needs && count($needs) > 0)
                        @foreach($needs as $need)
                            <div class="card px-4 py-5 sm:px-5 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-slate-800 dark:text-navy-50">
                                            {{ $need['title'] ?? 'Sans titre' }}
                                        </h3>
                                        <p class="text-sm text-slate-600 dark:text-navy-300 mt-1">
                                            Statut: <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded">{{ $need['status'] ?? 'Publié' }}</span>
                                        </p>
                                    </div>
                                </div>

                                <p class="text-slate-700 dark:text-navy-200 mb-4 line-clamp-2">
                                    {{ $need['description'] ?? 'Pas de description' }}
                                </p>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4 mb-4 text-sm">
                                    @if($need['closingDate'])
                                        <div>
                                            <p class="text-slate-600 dark:text-navy-300">Clôture:</p>
                                            <p class="font-semibold text-slate-800 dark:text-navy-50">
                                                {{ \Carbon\Carbon::parse($need['closingDate'])->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    @endif

                                    @if($need['profiles'] && is_array($need['profiles']))
                                        <div>
                                            <p class="text-slate-600 dark:text-navy-300">Profils:</p>
                                            <p class="font-semibold text-slate-800 dark:text-navy-50">{{ implode(', ', $need['profiles']) }}</p>
                                        </div>
                                    @endif

                                    <div>
                                        <p class="text-slate-600 dark:text-navy-300">Candidatures:</p>
                                        <p class="font-semibold text-slate-800 dark:text-navy-50">
                                            {{ count($need['applications'] ?? []) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-3 justify-end pt-4 border-t border-slate-200 dark:border-navy-500">
                                    <a href="{{ route('needs.show', $need['id']) }}"
                                       class="btn rounded-lg bg-primary px-4 py-2 font-medium text-white hover:bg-primary-focus dark:bg-accent dark:hover:bg-accent-focus text-sm">
                                        <svg class="inline-block h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Consulter
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="card px-4 py-12 sm:px-5 text-center">
                            <svg class="inline-block h-16 w-16 text-slate-400 dark:text-navy-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-slate-600 dark:text-navy-300 text-lg">Aucun besoin trouvé</p>
                            <p class="text-slate-500 dark:text-navy-400 mt-2">Revenez bientôt pour découvrir de nouveaux besoins</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>
        </div>

    </main>

</x-app-layout>
