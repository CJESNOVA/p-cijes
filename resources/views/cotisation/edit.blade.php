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
                    <div class="alert flex rounded-lg bg-danger px-4 py-4 text-white sm:px-5">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card px-4 pb-4 sm:px-5">
                    <div class="max-w-xxl">

                        {{-- FORMULAIRE --}}
                        <form method="POST" action="{{ route('cotisation.update', $cotisation->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6">

                                {{-- TYPE --}}
                                <div>
                                    <label class="block">
                                        <span>Type de cotisation <span class="text-danger">*</span></span>
                                        <select name="cotisationtype_id" required class="form-select mt-1.5 w-full rounded-lg">
                                            <option value="">Sélectionner un type</option>
                                            @foreach($cotisationtypes as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ old('cotisationtype_id', $cotisation->cotisationtype_id) == $type->id ? 'selected' : '' }}>
                                                    {{ $type->titre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>

                                {{-- DEVISE --}}
                                <div>
                                    <label class="block">
                                        <span>Devise <span class="text-danger">*</span></span>
                                        <select name="devise" required class="form-select mt-1.5 w-full rounded-lg">
                                            @foreach(['FCFA','XOF','EUR','USD'] as $devise)
                                                <option value="{{ $devise }}"
                                                    {{ old('devise', $cotisation->devise) === $devise ? 'selected' : '' }}>
                                                    {{ $devise }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>

                                {{-- MONTANT --}}
                                <div>
                                    <label class="block">
                                        <span>Montant total <span class="text-danger">*</span></span>
                                        <input type="number" step="0.01" min="0" name="montant" required
                                            value="{{ old('montant', $cotisation->montant) }}"
                                            class="form-input mt-1.5 w-full rounded-lg">
                                    </label>
                                </div>

                                {{-- DATES --}}
                                <div>
                                    <label class="block">
                                        <span>Date de début *</span>
                                        <input type="date" name="date_debut" required
                                            value="{{ old('date_debut', optional($cotisation->date_debut)->format('Y-m-d')) }}"
                                            class="form-input mt-1.5 w-full rounded-lg">
                                    </label>
                                </div>

                                <div>
                                    <label class="block">
                                        <span>Date de fin *</span>
                                        <input type="date" name="date_fin" required
                                            value="{{ old('date_fin', optional($cotisation->date_fin)->format('Y-m-d')) }}"
                                            class="form-input mt-1.5 w-full rounded-lg">
                                    </label>
                                </div>

                                <div>
                                    <label class="block">
                                        <span>Date d’échéance *</span>
                                        <input type="date" name="date_echeance" required
                                            value="{{ old('date_echeance', optional($cotisation->date_echeance)->format('Y-m-d')) }}"
                                            class="form-input mt-1.5 w-full rounded-lg">
                                    </label>
                                </div>

                                {{-- MODE PAIEMENT --}}
                                <div>
                                    <label class="block">
                                        <span>Mode de paiement</span>
                                        <input type="text" name="mode_paiement"
                                            value="{{ old('mode_paiement', $cotisation->mode_paiement) }}"
                                            class="form-input mt-1.5 w-full rounded-lg">
                                    </label>
                                </div>

                                {{-- COMMENTAIRES --}}
                                <div class="sm:col-span-2">
                                    <label class="block">
                                        <span>Commentaires</span>
                                        <textarea name="commentaires" rows="4"
                                            class="form-input mt-1.5 w-full rounded-lg">{{ old('commentaires', $cotisation->commentaires) }}</textarea>
                                    </label>
                                </div>
                            </div>

                            {{-- ÉTAT --}}
                            <div class="bg-slate-50 dark:bg-navy-800 rounded-lg p-4 border mt-6">
                                <h4 class="text-sm font-semibold mb-2">État actuel</h4>
                                <div class="grid md:grid-cols-3 gap-4 text-sm">
                                    <div>Payé : <strong>{{ number_format($cotisation->montant_paye,2) }} {{ $cotisation->devise }}</strong></div>
                                    <div>Restant : <strong>{{ number_format($cotisation->montant_restant,2) }} {{ $cotisation->devise }}</strong></div>
                                    <div>Statut : <strong>{{ $cotisation->statut_label }}</strong></div>
                                </div>
                            </div>

                            {{-- ACTIONS --}}
                            <div class="flex justify-end space-x-4 mt-6">
                                <a href="{{ route('cotisation.index') }}" class="btn bg-slate-200">
                                    Annuler
                                </a>
                                <button type="submit" class="btn bg-primary text-white">
                                    Mettre à jour
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-span-12 py-6 lg:col-span-4">
                @include('layouts.sidebar')
            </div>
        </div>
    </main>
</x-app-layout>
