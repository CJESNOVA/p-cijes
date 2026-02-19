<div class="mt-4 ml-4 grid grid-cols-12 gap-4" style="margin-left: 30px;">
    <!-- Section principale -->
    <div class="col-span-12 lg:col-span-12 space-y-6">
        
        <!-- Ligne 1 : Bienvenue + Revenu -->
        <div class="grid grid-cols-12 gap-6">

            {{-- 🧑‍💼 Card 1 : Bienvenue --}}
            <div class="col-span-12 lg:col-span-6">
                <div class="bg-white shadow-md rounded-xl p-6 space-y-3">
                    <p class="text-xl opacity-80">
                        Bonjour, <span class="font-semibold">{{ $membre->prenom ?? '' }} {{ $membre->nom ?? '' }}</span> !
                    </p>
                    <p class="text-xs opacity-70">
                        Membre depuis le {{ optional($membre->created_at)->format('d/m/Y') }}
                    </p>
                    <div class="mt-3 space-y-1">
                        <p class="text-sm">Email : {{ $membre->email ?? '-' }}</p>
                        <p class="text-sm text-blue-600">{{ $membre->membretype->titre ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- 💰 Card 2 : Revenu + Bouton de synchro --}}
            <div class="col-span-12 lg:col-span-6">
                <div class="bg-white shadow-md rounded-xl p-6 flex flex-col justify-between space-y-4">
                    <div>
                        <!-- <p class="text-sm font-medium opacity-80">Revenu ce mois</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['revenue_month'] ?? 0) }} FCFA</p>
                        <p class="mt-1 text-sm opacity-70">
                            +{{ $stats['revenue_variation'] ?? 0 }}% par rapport au mois précédent 
                        </p> -->
                        
                        @if(isset($stats['solde_type1']) && $stats['solde_type1'] > 0)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-sm font-medium opacity-80">Solde disponible</p>
                                <p class="text-xl font-semibold text-green-600">{{ number_format($stats['solde_type1'], 2) }} FCFA</p>
                            </div>
                        @endif
                    </div>

                    <a href="https://academy.cjes.africa/login" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center px-6 py-3 bg-[#1DA8BB] text-white rounded-lg hover:bg-[#1DA8BB]/90 transition-colors shadow-lg">
                        Accéder à CJES Academy
                    </a>

                    <!-- <div class="flex items-center justify-between mt-4">
                        <button id="btnSyncSupabase" 
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            🔁 Synchroniser avec Supabase
                        </button>
                        <span id="syncStatus" class="text-xs text-slate-500">Dernière synchro : jamais</span>
                    </div> -->
                </div>
            </div>
        </div>

        <!-- Ligne 2 : Petites cartes de stats -->
        <!-- <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $smallStats = [
                    ['title' => 'Inscriptions', 'value' => $stats['inscriptions'] ?? 0, 'bg' => 'bg-[#1DA8BB]/10', 'iconColor' => 'text-[#1DA8BB]', 'icon' => 'M3 3v18h18'],
                    ['title' => 'Entreprises', 'value' => $stats['entreprises'] ?? 0, 'bg' => 'bg-[#12CEB7]/10', 'iconColor' => 'text-[#12CEB7]', 'icon' => 'M3 3v18h18'],
                    ['title' => 'Experts', 'value' => $stats['experts'] ?? 0, 'bg' => 'bg-[#F09116]/10', 'iconColor' => 'text-[#F09116]', 'icon' => 'M3 3v18h18'],
                    ['title' => 'Diagnostics', 'value' => $stats['diagnostics'] ?? 0, 'bg' => 'bg-[#9333EA]/10', 'iconColor' => 'text-[#9333EA]', 'icon' => 'M3 3v18h18'],
                ];
            @endphp

            @foreach($smallStats as $stat)
            <div class="bg-white shadow-md rounded-xl p-5 flex items-center space-x-3 hover:scale-105 transition-transform duration-300">
                <div class="p-3 {{ $stat['bg'] }} rounded-full">
                    <svg class="w-6 h-6 {{ $stat['iconColor'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400">{{ $stat['title'] }}</p>
                    <p class="text-xl font-semibold">{{ $stat['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div> -->
    </div>

    <!-- Ligne 3 : Autres widgets -->
    <!-- <div class="col-span-12 lg:col-span-12 grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
        @php
            $widgets = [
                ['title' => 'Entreprises Pépites', 'value' => $stats['pepite'] ?? 0, 'colors' => 'from-[#F09116] to-[#F09116]/80', 'icon' => '⭐'],
                ['title' => 'Membres associés', 'value' => $stats['membres_associes'] ?? 0, 'colors' => 'from-[#9333EA] to-[#9333EA]/80', 'icon' => '👥'],
                ['title' => 'PME', 'value' => $stats['pme'] ?? 0, 'colors' => 'from-[#12CEB7] to-[#12CEB7]/80', 'icon' => '🏢'],
            ];
        @endphp

        @foreach($widgets as $widget)
        <div class="bg-gradient-to-r {{ $widget['colors'] }} text-white rounded-xl p-5 shadow-lg flex items-center justify-between hover:scale-105 transition-transform duration-300">
            <div class="flex flex-col">
                <p class="text-xs opacity-90">{{ $widget['title'] }}</p>
                <p class="text-2xl font-bold">{{ $widget['value'] }}</p>
            </div>
            <div class="text-3xl">{{ $widget['icon'] }}</div>
        </div>
        @endforeach
    </div> -->
</div>


{{-- 🔁 Script de synchronisation Supabase --}}
<script>
function updateSyncTime() {
    const status = document.getElementById('syncStatus');
    if (status) {
        status.innerText = "Dernière synchro : " + new Date().toLocaleTimeString();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnSyncSupabase');
    const intervalMinutes = 10; // ⏱️ synchro toutes les 10 minutes
    const routeUrl = "{{ route('sync.supabase') }}";

    async function syncSupabase(manuel = false) {
        try {
            const response = await fetch(routeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            console.log('✅ Synchronisation Supabase réussie', data);

            updateSyncTime();

            if (manuel) {
                showToast(data.message || 'Synchronisation terminée avec succès.', 'success');
            }
        } catch (error) {
            console.error('❌ Erreur synchro Supabase', error);
            if (manuel) showToast('Erreur pendant la synchronisation.', 'error');
        }
    }

    // 🔘 Bouton manuel
    if (btn) btn.addEventListener('click', () => syncSupabase(true));

    // 🔁 Exécution automatique toutes les X minutes
    setInterval(() => syncSupabase(false), intervalMinutes * 60 * 1000);
});

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-6 right-6 px-4 py-3 rounded shadow-lg text-white transition-opacity duration-500 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    toast.innerText = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}
</script>
