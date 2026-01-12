<x-app-layout title="Cotisations payées" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Cotisations payées
            </h2>
            <div class="hidden h-full py-1 sm:flex">
                <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert flex rounded-lg bg-danger px-4 py-4 text-white sm:px-5">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                
                @if($cotisationressources->isNotEmpty())
                    <div class="card">
                        <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                            <table class="is-hoverable w-full text-left">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-navy-800">
                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Date</th>
                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Type</th>
                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Entreprise</th>
                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Montant</th>
                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Référence</th>
                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Statut</th>
                                        <th class="px-3 py-2 text-center font-medium text-slate-700 dark:text-navy-200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cotisationressources as $cotisationressource)
                                        <tr class="border-b">
                                            <td class="px-3 py-2">
                                                {{ $cotisationressource->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    {{ $cotisationressource->cotisation->cotisationtype->titre ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2">
                                                {{ $cotisationressource->cotisation->entreprise->nom ?? '-' }}
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="font-medium">{{ number_format($cotisationressource->montant, 2) }}</span>
                                                <span class="text-slate-600 dark:text-navy-400"> XOF</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <code class="text-xs bg-slate-100 dark:bg-navy-800 px-2 py-1 rounded">
                                                    {{ $cotisationressource->reference }}
                                                </code>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success/10 text-success dark:bg-success/20 dark:text-success-light">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Payée
                                                </span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('cotisationressource.show', $cotisationressource->id) }}" 
                                                       class="btn bg-info text-white hover:bg-info-focus text-xs px-2 py-1 rounded">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body text-center py-12">
                            <i class="fas fa-receipt text-6xl text-slate-400 mb-6"></i>
                            <h3 class="text-2xl font-bold text-slate-800 dark:text-navy-100 mb-4">
                                Aucune cotisation payée
                            </h3>
                            <p class="text-slate-600 dark:text-navy-400 mb-8">
                                Vous n'avez pas encore payé de cotisations via votre ressource KOBO.
                            </p>
                            <a href="{{ route('cotisation.index') }}" 
                               class="btn bg-primary text-white hover:bg-primary-focus">
                                <i class="fas fa-plus mr-2"></i>
                                Payer une cotisation
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    
        </div>
    </main>
</x-app-layout>
