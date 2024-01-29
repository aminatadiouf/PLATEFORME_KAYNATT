<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Tontine;
use App\Models\GestionCycle;
use Illuminate\Http\Request;
use App\Models\CotisationTontine;
use App\Http\Controllers\Controller;
use App\Models\ParticipationTontine;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateCotisationTontineRequest;

class CotisationTontineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    
    // public function effectuerCotisation(Request $request, ParticipationTontine $participationTontines,Tontine $tontine)
    // {

    //     $cotisations = new CotisationTontine();
    //     $participationTontines = ParticipationTontine::all();
    //     $user = Auth::user();

    //     $cycleCorrespondant = Tontine::FindOrFail($tontine->id);
       
    //     $cycle = $cycleCorrespondant->tontine->gestion_cycles()
    //     ->orderBy('nombre_de_cycle', 'desc')
    //         ->first()
    //         ->id;


    //     foreach ($participationTontines as $participationTontine) {
    //             if($user->id === $participationTontine->user_id && $participationTontine->statutParticipation ==='accepte')
    //             {
                    
    //                 $cotisations->montant_paiement = $request->montant_paiement;
                    
    //                 $cotisations ->participationTontine_id=$user->id;
    //                 $cotisations->gestion_cycle_id = $cycle;
    //                 dd($participationTontine->tontine[0]->montant);
    //                 if($cotisations->montant_paiement === $participationTontine->tontine[0]->montant)
    //                 {
    //                     $cotisations->save();

    //                     return response()->json([
    //                         'statut_code'=> 200,
    //                         'statut_message'=> 'votre cotisation a été effectué avec succés',
    //                         'data'=>$cotisations
    //                     ]);

    //                 }else{

    //                     return response()->json([
    //                         'statut_code'=> false,
    //                         'statut_message'=> 'le montant est inférieur au montant donné par le créateur',
    //                     ]);
                    
    //                 }
                      
    //             }
    //     }         
    // }

    public function effectuerCotisation(Request $request, CotisationTontine $cotisations,ParticipationTontine $participationTontines)

    {
        $user = Auth::user();

       $participationTontine = ParticipationTontine::FindOrFail($participationTontines->id);
    //   $participationTontine = ParticipationTontine::FindOrFail(4);
    
       $montantUser = $participationTontine->tontine->montant;

        if($request->montant_paiement != $montantUser )


        {
         return response()->json([
 
             'statut_code' => false,
             'statut_message' => 'Le montant de la cotisation est différent du montant de la tontine.',
         ]);
        }


        $cycle = $participationTontine->tontine->gestion_cycles()
        ->orderBy('id', 'asc')
            ->first()
            ->id;

            if($participationTontine->tontine->etat ==='en_attente')
            {
                
                return response()->json([
                    'statut_code'=> false,
                    'statut_message'=> 'la tontine n\'est pas encore en cours',
                   
                ]);
            }

            if($participationTontine->user_id != $user->id)
            {
                return response()->json([
                    'statut_code'=> false,
                    'statut_message'=> 'l\'utilisateur n\'est pas un participant de la tontine ',
                   
                ]);
            }
       if($participationTontine->statutParticipation !='accepte')
       {
        return response()->json([
            'statut_code'=> false,
            'statut_message'=> 'l\'utilisateur n\'est pas encore accepté à participer à la tontine  ',
           
        ]);
       }
// dd(       $participationTontine->tontine->etat ==='en_cours')
// ;
            
       if ($participationTontine->user_id === $user->id &&  $participationTontine->statutParticipation ==='accepte' &&
       $participationTontine->tontine->etat ==='en_cours')

       {
       $cotisations = new CotisationTontine();

       
       $cotisations ->date_paiement = $request->date_paiement;
       $cotisations->montant_paiement = $request->montant_paiement;
       $cotisations->participation_Tontine_id = $user->id;
       $cotisations->gestion_cycle_id = $cycle;

       $cotisations->save();

       return response()->json([
                                'statut_code'=> 200,
                                'statut_message'=> 'votre cotisation a été effectué avec succés',
                                'data'=>$cotisations
                            ]);
    }


    }

    

    public function faireTirage(User $user)
    {
        // Récupérer la liste des utilisateurs non-gagnants
        $nonGagnants = User::where('gagnant', false)->get();

        if ($nonGagnants->isEmpty()) {
            return response()->json(['message' => 'Il n\'y a pas de non-gagnants pour le moment.'], 404);
        }

        // Effectuer le tirage au sort
        $gagnant = $nonGagnants->random();

        // Mettre à jour le statut du gagnant dans la base de données
        $gagnant->update(['gagnant' => true]);

        // Notifier le gagnant (vous devrez implémenter la logique de notification)
        $this->notifierGagnant($gagnant);

        return response()->json(['gagnant' => $gagnant, 'message' => 'Le tirage au sort a été effectué avec succès.']);
    }


   

    // public function effectuerCotisation(Request $request, ParticipationTontine $participationTontine, Tontine $tontine, GestionCycle $gestionCycle)
    // {
    //     $user = Auth::user();
    
    //     // Trouver le dernier cycle de gestion pour cette tontine
    //     $cycle = Tontine::FindOrFail($tontine->id);
    //     $dernierCycle = $cycle->gestion_cycles()->orderBy('nombre_de_cycle', 'desc')->first();
    // dd($dernierCycle);
    //     if (!$dernierCycle) {
    //         // Aucun cycle de gestion trouvé, renvoyer une réponse d'erreur
    //         return response()->json([
    //             'statut_code' => false,
    //             'statut_message' => 'Aucun cycle de gestion trouvé pour cette tontine.',
    //         ]);
    //     }
    
    //     // Utiliser la date du dernier cycle de gestion pour la date de paiement
    //     $datePaiement = $dernierCycle->date_cycle;
    
    //     // Vérifier si l'utilisateur a une participation acceptée à cette tontine
    //     $participationTontine = ParticipationTontine::FindOrFail($participationTontine->id);
    //     $participation = $participationTontine->where('user_id', $user->id)
    //                                           ->where('statutParticipation', 'accepte')
    //                                           ->first();
    
    //     if (!$participation) {
    //         // Aucune participation acceptée trouvée pour cet utilisateur, renvoyer une réponse d'erreur
    //         return response()->json([
    //             'statut_code' => false,
    //             'statut_message' => 'Vous n\'êtes pas autorisé à effectuer une cotisation pour cette tontine.',
    //         ]);
    //     }
    
    //     // Comparer le montant de la cotisation avec le montant de la tontine
    //     if ($request->montant_paiement != $tontine->montant) {
    //         return response()->json([
    //             'statut_code' => false,
    //             'statut_message' => 'Le montant de la cotisation est différent du montant de la tontine.',
    //         ]);
    //     }
    
    //     // Enregistrer la cotisation pour le dernier cycle de gestion
    //     $cotisation = new CotisationTontine();
    //     $cotisation->date_paiement = $datePaiement;
    //     $cotisation->montant_paiement = $request->montant_paiement;
    //     $cotisation->participationTontine_id = $participation->id;
    //     $cotisation->gestion_cycle_id = $dernierCycle->id;
    //     $cotisation->save();
    
    //     // Retourner une réponse de succès
    //     return response()->json([
    //         'statut_code' => 200,
    //         'statut_message' => 'Votre cotisation a été effectuée avec succès.',
    //         'data' => $cotisation,
    //     ]);
    // }
    


        public function listeCotisation()
        {
            return response()->json([
                'statut_code'=> 200,
                'statut_message'=> 'votre cotisation a été effectué avec succés',
                'data'=>CotisationTontine::all(),
            ]);
        }

        



        public function cotisationParparticipation(ParticipationTontine $participations)
        {
            $participation = ParticipationTontine :: FindOrFail($participations->id);
            $cotisations = CotisationTontine :: all();

            
            
           // $participation->cotisationTontines;

    
           
            return response()->json([
                'statut_code'=> 200,
                'statut_message'=>'la liste de tous les cotisations',
                'data'=>$participation,
            ]);
        }
        


        
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //jghyg 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
