<x-app-layout title="Mes Crédits disponibles" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Mes Crédits disponibles
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
            <li>Mes Crédits disponibles</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

                <!-- Définition de SIKA -->
                <div class="card bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 p-6 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-blue-900 mb-2">Qu'est-ce que SIKA ?</h3>
                            <div class="text-sm text-blue-800 space-y-2">
                                <p><strong>SIKA</strong> est un dispositif de <strong>financement</strong> mis en place par CJES Africa pour soutenir les entrepreneurs et porteurs de projets.</p>
                                <div class="bg-white/50 rounded-lg p-3 mt-3">
                                    <h4 class="font-semibold text-blue-900 mb-1">🎯 Objectifs de SIKA :</h4>
                                    <ul class="list-disc list-inside space-y-1 text-blue-800">
                                        <li>Financer le démarrage d'activités économiques</li>
                                        <li>Soutenir la croissance des PME existantes</li>
                                        <li>Faciliter l'accès au crédit pour les entrepreneurs</li>
                                        <li>Promouvoir l'entrepreneuriat local</li>
                                    </ul>
                                </div>
                                <div class="bg-white/50 rounded-lg p-3 mt-3">
                                    <h4 class="font-semibold text-blue-900 mb-1">💡 Comment ça marche ?</h4>
                                    <ol class="list-decimal list-inside space-y-1 text-blue-800">
                                        <li>Soumettez votre demande de financement</li>
                                        <li>Votre dossier est étudié par le comité de validation</li>
                                        <li>En cas d'acceptation, les fonds sont débloqués</li>
                                        <li>Vous bénéficiez d'un accompagnement personnalisé</li>
                                    </ol>
                                </div>
                                <p class="mt-3"><strong>💸 Pour faire une demande :</strong> Utilisez l'option "Demande SIKA" dans le menu "Ressources" de votre espace personnel.</p>
                            </div>
                        </div>
                    </div>
                </div>

@if($credits)
              <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">



    @if($credits->isEmpty())
        <p>Aucun crédit trouvé.</p>
    @else
                        
                @foreach($credits as $credit)
                <div class="card p-4">
                    <h3 class="font-bold mt-2">
                        Entreprise : {{ $credit->entreprise->nom ?? '—' }}
                    </h3>

                    <p class="text-sm mt-2">
                        Montant total : 
                        <span class="font-semibold">{{ number_format($credit->montanttotal, 0, ',', ' ') }} FCFA</span>
                    </p>

                    <p class="text-sm mt-2">
                        Montant utilisé : 
                        <span class="text-red-600">{{ number_format($credit->montantutilise, 0, ',', ' ') }} FCFA</span>
                    </p>

                    <p class="text-sm mt-2">
                        Solde restant : 
                        <span class="text-blue-600 font-bold">
                            {{ number_format($credit->montanttotal - $credit->montantutilise, 0, ',', ' ') }} FCFA
                        </span>
                    </p>

                    <p class="text-sm mt-2">
                        Type : {{ $credit->credittype->titre ?? '—' }}
                    </p>

                    <p class="text-sm mt-2">
                        Statut : 
                        <span class="px-2 py-1 rounded text-white 
                            {{ $credit->creditstatut->titre == 'Actif' ? 'bg-green-500' : 'bg-red-500' }}">
                            {{ $credit->creditstatut->titre ?? '—' }}
                        </span>
                    </p>

                    <p class="text-sm mt-2">
                        Partenaire : {{ $credit->partenaire->titre ?? '—' }}
                    </p>

                    <p class="text-sm mt-2">
                        Attribué par : {{ $credit->user->name ?? 'Admin' }}
                    </p>

                    <p class="text-xs mt-2 text-gray-500">
                        Délivré le {{ \Carbon\Carbon::parse($credit->datecredit)->format('d/m/Y') }}
                    </p>
                    
                <br />
                    <a href="#" class="btn bg-blue-500 text-white">A utiliser</a>
                    
                </div>
                @endforeach
        @endif
          </div>
@endif
          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>