{{-- resources/views/dashboard/partials/messages.blade.php --}}
<div class="card p-4" style="margin-left: 30px; margin-right: 30px;">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Messages récents</h3>
        <a href="{{ route('message.index') }}" class="text-xs text-[#1DA8BB] hover:underline">
            Voir tous
        </a>
    </div>

    <div class="mt-3 space-y-3">
        @forelse($messages->take(6) as $msg)
            <a href="{{ route('message.show', $msg->conversation->id) }}" 
               class="flex items-start space-x-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-600 transition">
               
                {{-- Avatar du membre expéditeur --}}
                <div class="avatar">
                    <img 
                        src="{{ $msg->membre && $msg->membre->vignette 
                                ? env('SUPABASE_BUCKET_URL') . '/' . $msg->membre->vignette 
                                : asset('images/200x200.png') }}" 
                        alt="Avatar"
                        class="rounded-full w-10 h-10 object-cover"
                    >
                </div>

                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-slate-700 dark:text-navy-100">
                                {{ $msg->membre->prenom ?? 'Utilisateur' }} {{ $msg->membre->nom ?? 'inconnu' }}
                            </p>
                            <p class="text-xs text-slate-400 dark:text-navy-300">
                                {{ \Illuminate\Support\Str::limit(strip_tags($msg->contenu), 80) }}
                            </p>
                        </div>

                        {{-- Date d’envoi --}}
                        <div class="text-xs text-slate-400 dark:text-navy-300 whitespace-nowrap ml-2">
                            {{ $msg->created_at ? $msg->created_at->diffForHumans() : '' }}
                        </div>
                    </div>

                    {{-- Statut du message --}}
                    @if(!$msg->lu)
                        <span class="inline-block mt-1 text-[10px] text-[#1DA8BB] font-medium">Non lu</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="p-3 bg-yellow-50 text-slate-700 rounded text-sm dark:bg-navy-700/40 dark:text-navy-200">
                Aucun message récent
            </div>
        @endforelse
    </div>
</div>
