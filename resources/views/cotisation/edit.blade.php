<x-app-layout title="Modifier une cotisation" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Modifier une cotisation
            </h2>
            <div class="hidden h-full py-1 sm:flex">
                <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                
                @if(session('error'))
                    <div class="alert flex rounded-lg bg-danger px-4 py-4 text-white sm:px-5">{{ session('error') }}</div>
                @endif

                <div class="card px-4 pb-4 sm:px-5">
                    <div class="max-w-xxl">
                        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6">
                            <div>
                                <label class="block">
                                    <span>Type de cotisation <span class="text-danger">*</span></span>
                                    <select name="cotisationtype_id" required
                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                        <option value="">Sélectionner un type</option>
                                        @foreach($cotisationtypes as $type)
                                            <option value="{{ $type->id }}" {{ $cotisation->cotisationtype_id == $type->id ? 'selected' : '' }}>
                                                {{ $type->titre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>

                            <div>
                                <label class="block">
                                    <span>Devise <span class="text-danger">*</span></span>
                                    <select name="devise" required
                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                        <option value="XOF" {{ $cotisation->devise == 'XOF' ? 'selected' : '' }}>XOF</option>
                                        <option value="EUR" {{ $cotisation->devise == 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="USD" {{ $cotisation->devise == 'USD' ? 'selected' : '' }}>USD</option>
                                    </select>
                                </label>
                            </div>

                            <div>
                                <label class="block">
                                    <span>Montant total <span class="text-danger">*</span></span>
                                    <input type="number" name="montant" step="0.01" min="0" required
                                           value="{{ $cotisation->montant }}"
                                           class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                           placeholder="0.00">
                                </label>
                            </div>

                            <div>
                                <label class="block">
                                    <span>Date de début <span class="text-danger">*</span></span>
                                    <input type="date" name="date_debut" required
                                           value="{{ $cotisation->date_debut->format('Y-m-d') }}"
                                           class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                </label>
                            </div>

                            <div>
                                <label class="block">
                                    <span>Date de fin <span class="text-danger">*</span></span>
                                    <input type="date" name="date_fin" required
                                           value="{{ $cotisation->date_fin->format('Y-m-d') }}"
                                           class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                </label>
                            </div>

                            <div>
                                <label class="block">
                                    <span>Date d'échéance <span class="text-danger">*</span></span>
                                    <input type="date" name="date_echeance" required
                                           value="{{ $cotisation->date_echeance->format('Y-m-d') }}"
                                           class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                </label>
                            </div>

                            <div>
                                <label class="block">
                                    <span>Mode de paiement</span>
                                    <input type="text" name="mode_paiement" maxlength="50"
                                           value="{{ $cotisation->mode_paiement }}"
                                           class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                           placeholder="Espèces, virement, mobile money...">
                                </label>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block">
                                    <span>Commentaires</span>
                                    <textarea name="commentaires" rows="4" maxlength="1000"
                                              class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">{{ $cotisation->commentaires }}</textarea>
                                </label>
                            </div>
                        </div>

                        <div class="bg-slate-50 dark:bg-navy-800 rounded-lg p-4 border border-slate-200 dark:border-navy-700 mt-6">
                            <h4 class="text-sm font-semibold text-slate-700 dark:text-navy-200 mb-2">
                                État actuel de la cotisation
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-slate-600 dark:text-navy-400">Montant payé :</span>
                                    <span class="font-medium text-slate-800 dark:text-navy-100">{{ number_format($cotisation->montant_paye, 2) }} {{ $cotisation->devise }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-600 dark:text-navy-400">Restant à payer :</span>
                                    <span class="font-medium text-slate-800 dark:text-navy-100">{{ number_format($cotisation->montant_restant, 2) }} {{ $cotisation->devise }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-600 dark:text-navy-400">Statut :</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @switch($cotisation->statut)
                                            @case('en_attente')
                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @break
                                            @case('paye')
                                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @break
                                            @case('partiel')
                                                bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @break
                                            @case('retard')
                                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @break
                                        @endswitch
                                    ">
                                        {{ $cotisation->statut_label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 mt-6">
                            <a href="{{ route('cotisation.index') }}" 
                               class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                <i class="fas fa-save mr-2"></i>
                                Mettre à jour
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    
        </div>
    </main>
</x-app-layout>
