<x-app-layout title="Profil Expert" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">Profil Expert</h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">Consultez les informations et les disponibilités de l'expert</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

        <div class="bg-white dark:bg-navy-800 shadow-lg rounded-xl p-6 space-y-8">

    {{-- Infos Expert --}}
    <div class="flex items-center space-x-4 border-b pb-4">
        <div class="h-16 w-16 rounded-full bg-primary text-white flex items-center justify-center text-2xl font-bold">
            {{ strtoupper(substr($expert->membre->nom ?? 'X',0,1)) }} {{ strtoupper(substr($expert->membre->prenom ?? 'X',0,1)) }}
        </div>
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">
                {{ $expert->membre->nom ?? 'Expert inconnu' }} {{ $expert->membre->prenom ?? 'Expert inconnu' }}
            </h2>
            <p class="text-slate-500 dark:text-slate-400">
                {{ $expert->secteur->titre ?? 'Secteur non défini' }}  
                <span class="text-primary">|</span> {{ $expert->experttype->titre ?? 'Type non défini' }}  
                <span class="text-primary">|</span> Domaine : {{ $expert->domaine }}
            </p>
        </div>
    </div>

    {{-- Disponibilités sous forme de calendrier --}}
    <div>
        <h3 class="text-lg font-semibold text-slate-700 dark:text-white mb-3">Disponibilités</h3>
        @if($expert->disponibilites->count())
            <div class="grid grid-cols-7 gap-2 text-center text-sm">
                @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $day)
                    <div class="font-medium text-slate-600 dark:text-slate-300">{{ $day }}</div>
                @endforeach
                @foreach(['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'] as $jour)
                    <div class="border rounded-lg p-2 bg-slate-50 dark:bg-navy-700">
                        @php
                            $disposJour = $expert->disponibilites->filter(fn($d) => strtolower($d->jour->titre) === strtolower($jour));
                        @endphp
                        @forelse($disposJour as $dispo)
                            <p class="text-xs bg-primary text-white rounded px-2 py-1 mb-1">
                                {{ \Carbon\Carbon::parse($dispo->horairedebut)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($dispo->horairefin)->format('H:i') }}
                            </p>
                        @empty
                            <span class="text-slate-400 text-xs">-</span>
                        @endforelse
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-slate-400">Aucune disponibilité enregistrée.</p>
        @endif
    </div>

    {{-- Évaluations avec étoiles --}}
    <div>
    <h3 class="text-lg font-semibold text-slate-700 dark:text-white mb-3">Évaluations</h3>

    @forelse($expert->evaluations as $eval)
        <div class="mb-4 border rounded-lg p-4 bg-slate-50 dark:bg-navy-700 dark:border-navy-600">
            {{-- Nom du membre --}}
            <p class="font-medium text-primary">
                {{ trim(($eval->membre->nom ?? '') . ' ' . ($eval->membre->prenom ?? '')) ?: 'Utilisateur' }}
            </p>

            {{-- Note sous forme d’étoiles --}}
            <div class="flex items-center space-x-1 my-2">
                @for($i = 1; $i <= 5; $i++)
                    <svg xmlns="http://www.w3.org/2000/svg" 
                            class="w-5 h-5 {{ $i <= $eval->note ? 'text-yellow-400' : 'text-gray-300' }}" 
                            fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.868 
                                    1.48 8.241L12 18.896l-7.416 4.519 
                                    1.48-8.241L0 9.306l8.332-1.151z"/>
                        </svg>
                @endfor
            </div>

            {{-- Commentaire facultatif --}}
            @if(!empty($eval->commentaire))
                <p class="text-slate-600 dark:text-slate-300 italic">
                    « {{ $eval->commentaire }} »
                </p>
            @endif
        </div>
    @empty
        <p class="text-slate-400 italic">Aucune évaluation pour cet expert.</p>
    @endforelse
</div>


    {{-- Fichier joint --}}
    @if ($expert->fichier)
        <div class="mt-6">
            <a href="{{ env('SUPABASE_BUCKET_URL') . '/' . $expert->fichier }}" target="_blank"
               class="btn bg-primary text-white hover:bg-primary-focus rounded-lg">
                📂 Voir le fichier joint
            </a>
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

