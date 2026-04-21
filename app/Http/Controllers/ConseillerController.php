<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestation;
use App\Models\Formation;
use App\Models\Conseillerprescription;
use App\Models\Accompagnement;
use App\Models\Accompagnementconseiller;
use App\Models\Conseillerentreprise;
use App\Models\Entreprisemembre;
use App\Models\Pays;
use App\Models\Membre;
use App\Models\Conseiller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConseillerController extends Controller
{
    
    public function mesConseillers()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();

        // Entreprises du membre
        $entrepriseIds = Entreprisemembre::where('membre_id', $membre->id)
            ->pluck('entreprise_id');

        // Accompagnements où le membre est bénéficiaire (direct ou via entreprise)
        $accompagnementIds = Accompagnement::where('membre_id', $membre->id)
            ->orWhereIn('entreprise_id', $entrepriseIds)
            ->pluck('id')
            ->toArray();

        // Accompagnements liés au membre en tant que conseiller
        $accompagnementConseillerIds = Accompagnementconseiller::whereHas('conseiller', function ($q) use ($membre) {
                $q->where('membre_id', $membre->id);
            })
            ->pluck('accompagnement_id')
            ->toArray();

        // Fusionner les deux listes
        $allAccompagnementIds = array_unique(array_merge($accompagnementIds, $accompagnementConseillerIds));

        // Récupérer les conseillers
        $conseillers = Accompagnementconseiller::with([
                'conseiller.membre',
                'conseiller.conseillertype',
                'conseiller.prescriptions.prestation',
                'conseiller.prescriptions.formation',
                'accompagnement.entreprise',
                'accompagnement.membre',
            ])
            ->whereIn('accompagnement_id', $allAccompagnementIds)
            ->whereHas('conseiller')
            ->get();

        return view('conseiller.mes_conseillers', compact('conseillers'));
    }



    public function index()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        $conseiller = Conseiller::where('membre_id', $membre->id)->first();
        $conseillerId = $conseiller->id;

        $prescriptions = Conseillerprescription::with([
            'conseiller.membre',
            'prestation',
            'entreprise',
            'membre'
        ])
        ->where('conseiller_id', $conseillerId)
        ->orderByDesc('id')
        ->get();

        return view('conseiller.index', compact('prescriptions'));
    }

    public function create()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        $conseiller = Conseiller::where('membre_id', $membre->id)->first();
        $conseillerId = $conseiller->id;

        $prestations = Prestation::where('pays_id', $membre->pays_id)
            ->where('etat', 1)
            ->get();

        $accompagnementconseillers = Accompagnementconseiller::with([
                'accompagnement.entreprise',
                'accompagnement.membre'
            ])
            ->where('conseiller_id', $conseillerId)
            ->orderByDesc('id')
            ->get();

        // Récupérer la liste des entreprises et des membres liés
        $entreprises = $accompagnementconseillers
            ->pluck('accompagnement.entreprise')
            ->filter()
            ->unique('id');

        $membres = $accompagnementconseillers
            ->pluck('accompagnement.membre')
            ->filter()
            ->unique('id');

        return view('conseiller.create', compact(
            'conseillerId',
            'entreprises',
            'membres',
            'prestations'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'conseiller_id' => 'required|exists:conseillers,id',
            'prestation_id' => 'required|exists:prestations,id',
            'entreprise_id' => 'nullable',
            'membre_id' => 'nullable',
        ]);

        // Vérifier qu’au moins entreprise_id ou membre_id est fourni
        if (empty($validated['entreprise_id']) && empty($validated['membre_id'])) {
            return back()
                ->withErrors(['destination' => 'Vous devez sélectionner soit une entreprise, soit un membre.'])
                ->withInput();
        }

        $validated['etat'] = 1;
        $validated['spotlight'] = 0;

        Conseillerprescription::create($validated);

        return redirect()
            ->route('conseiller.index')
            ->with('success', 'Prescription enregistrée avec succès.');
    }


    public function createFormation()
    {
        $userId = Auth::id();
        $membre = Membre::where('user_id', $userId)->firstOrFail();
        $conseiller = Conseiller::where('membre_id', $membre->id)->first();
        $conseillerId = $conseiller->id;

        $formations = Formation::where('pays_id', $membre->pays_id)
            ->where('etat', 1)
            ->get();

        $accompagnementconseillers = Accompagnementconseiller::with([
                'accompagnement.entreprise',
                'accompagnement.membre'
            ])
            ->where('conseiller_id', $conseillerId)
            ->orderByDesc('id')
            ->get();

        // Récupérer la liste des entreprises et des membres liés
        $entreprises = $accompagnementconseillers
            ->pluck('accompagnement.entreprise')
            ->filter()
            ->unique('id');

        $membres = $accompagnementconseillers
            ->pluck('accompagnement.membre')
            ->filter()
            ->unique('id');

        return view('conseiller.create_formation', compact(
            'conseillerId',
            'entreprises',
            'membres',
            'formations'
        ));
    }

    public function storeFormation(Request $request)
    {
        $validated = $request->validate([
            'conseiller_id' => 'required|exists:conseillers,id',
            'formation_id' => 'required|exists:formations,id',
            'entreprise_id' => 'nullable',
            'membre_id' => 'nullable',
        ]);

        // Vérifier qu’au moins entreprise_id ou membre_id est fourni
        if (empty($validated['entreprise_id']) && empty($validated['membre_id'])) {
            return back()
                ->withErrors(['destination' => 'Vous devez sélectionner soit une entreprise, soit un membre.'])
                ->withInput();
        }

        $validated['etat'] = 1;
        $validated['spotlight'] = 0;

        Conseillerprescription::create($validated);

        return redirect()
            ->route('conseiller.index')
            ->with('success', 'Prescription enregistrée avec succès.');
    }

}
