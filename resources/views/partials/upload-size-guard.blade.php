{{-- Bloque côté navigateur tout fichier dépassant la limite avant l'envoi du formulaire.
     Nécessaire car un fichier qui dépasse post_max_size/upload_max_filesize côté PHP
     fait échouer la requête avant même que Laravel ne démarre : aucun message
     d'erreur propre n'est alors possible côté serveur. --}}
@once
<script>
(function () {
    var MAX_BYTES = {{ \App\Support\UploadLimit::recommendedKilobytes() * 1024 }};
    var MAX_LABEL = @json(\App\Support\UploadLimit::recommendedLabel());

    function guard(input) {
        input.addEventListener('change', function () {
            for (var i = 0; i < input.files.length; i++) {
                if (input.files[i].size > MAX_BYTES) {
                    alert('Le fichier "' + input.files[i].name + '" dépasse la taille maximale autorisée (' + MAX_LABEL + ').');
                    input.value = '';
                    break;
                }
            }
        });
    }

    document.querySelectorAll('input[type="file"]').forEach(guard);
})();
</script>
@endonce
