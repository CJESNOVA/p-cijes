{{-- resources/views/dashboard/partials/system_health.blade.php --}}
<div class="card p-4">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Santé du système</h3>
    </div>

    <div class="mt-3 grid grid-cols-2 gap-3">
        <div class="rounded-lg border p-3">
            <p class="text-xs">HTTP</p>
            <p class="font-medium">OK</p>
            <div class="mt-2 h-1 bg-slate-100 rounded">
                <div class="w-8/12 h-1 rounded bg-success"></div>
            </div>
        </div>
        <div class="rounded-lg border p-3">
            <p class="text-xs">DB</p>
            <p class="font-medium">OK</p>
            <div class="mt-2 h-1 bg-slate-100 rounded">
                <div class="w-9/12 h-1 rounded bg-secondary"></div>
            </div>
        </div>
        <div class="rounded-lg border p-3">
            <p class="text-xs">Queue</p>
            <p class="font-medium">2 pending</p>
            <div class="mt-2 h-1 bg-slate-100 rounded">
                <div class="w-4/12 h-1 rounded bg-warning"></div>
            </div>
        </div>
        <div class="rounded-lg border p-3">
            <p class="text-xs">Disk</p>
            <p class="font-medium">75%</p>
            <div class="mt-2 h-1 bg-slate-100 rounded">
                <div class="w-3/4 h-1 rounded bg-error"></div>
            </div>
        </div>
    </div>
</div>
