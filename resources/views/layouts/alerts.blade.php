<div x-data="alertes()" x-init="init()" class="relative">
    <!-- Badge compteur -->
    <button @click="toggle()" class="relative px-3 py-2 bg-primary text-white rounded">
        Notifications <span x-text="unreadCount" class="ml-1 bg-red-500 text-white rounded-full px-2 text-xs"></span>
    </button>

    <!-- Liste des alertes -->
    <div x-show="open" 
         x-transition:enter="transition-all duration-300 ease-in-out"
         x-transition:enter-start="opacity-0 [transform:translate3d(1rem,0,0)]"
         x-transition:enter-end="opacity-100 [transform:translate3d(0,0,0)]"
         class="absolute right-0 mt-2 w-80 bg-white dark:bg-navy-700 shadow-lg rounded-lg overflow-y-auto max-h-96 p-4 space-y-3">

        <template x-for="alerte in alertes" :key="alerte.id">
            <div class="flex items-center space-x-3">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 dark:bg-accent-light/15">
                    <i class="fa-solid fa-gift text-primary"></i>
                </div>
                <div>
                    <p class="font-medium text-slate-600 dark:text-navy-100" x-text="alerte.title"></p>
                    <div class="mt-1 text-xs text-slate-400 line-clamp-1 dark:text-navy-300" x-text="alerte.message"></div>
                    <a :href="alerte.lienurl" class="text-blue-500 text-xs mt-1 inline-block">Voir</a>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function alertes() {
    return {
        open: false,
        alertes: [],
        unreadCount: 0,
        toggle() { this.open = !this.open },
        init() {
            // Charger les alertes initiales depuis Laravel
            fetch('{{ route("alertes.mesAlertes") }}')
                .then(res => res.json())
                .then(data => {
                    this.alertes = data.alertes;
                    this.unreadCount = data.unreadCount;
                });

            // Écouter les notifications broadcast en temps réel
            Echo.private('App.Models.Membre.{{ auth()->id() }}')
                .notification((notification) => {
                    this.alertes.unshift(notification);
                    this.unreadCount++;
                });
        }
    }
}
</script>
