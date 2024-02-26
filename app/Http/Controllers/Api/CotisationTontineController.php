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
use App\Models\Payment;
use OpenApi\Annotations as OA;


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


/**
 * Effectuer un paiement pour un cycle de gestion donné.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \App\Models\GestionCycle  $gestionCycle
 * @return \Illuminate\Http\Response
 *
 * @OA\Post(
 *     path="/auth/fairePaiement/{gestionCycle}",
 *     summary="Effectuer un paiement pour un cycle de gestion donné",
 *     description="Effectue un paiement pour un cycle de gestion donné dans le système.",
 *     operationId="effectuerPaiement",
 *     tags={"Paiement Participant"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="gestionCycle",
 *         in="path",
 *         description="ID du cycle de gestion",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données requises pour effectuer le paiement",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="date_paiement",
 *                 type="string",
 *                 format="date",
 *                 description="Date du paiement"
 *             ),
 *             @OA\Property(
 *                 property="montant_paiement",
 *                 type="number",
 *                 format="float",
 *                 description="Montant du paiement"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Succès - Le paiement a été effectué avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status_code",
 *                 type="integer",
 *                 example=200
 *             ),
 *             @OA\Property(
 *                 property="status_message",
 *                 type="string",
 *                 example="Le paiement a été effectué avec succès"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête incorrecte - Veuillez vérifier les paramètres de la requête"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - Veuillez vous connecter pour effectuer cette action"
 *     )
 * )
 */




    
 public function effectuerPaiement(Request $request,GestionCycle $gestionCycle)

 {
     $user= Auth::user();
     // dd($user->id);
     $gestionCycles= GestionCycle::FindOrFail($gestionCycle->id);

     $tontines =$gestionCycles->tontine()->get();
    //  dd( $tontines );
     foreach($tontines  as $tontine){
     $montantTontine =$tontine->montant;
// dd($montantTontine);

if(intval($montantTontine) != intval($request->montant_paiement))
     {
         return response()->json([
             'statut_code'=> false,
             'statut_message'=> 'cette montant n\'est pas celle du tontine ', 
         ]);
     }
     
 //    return response()->json(["date"=>$date]);
 $date =  date("Y-m-d", strtotime($request->date_paiement));
     if($date < $gestionCycle->date_cycle || $date > $gestionCycle->date_cycle)
     {
         return response()->json([
             'statut_code'=> false,
             'statut_message'=> 'veuillez effectuer votre cotisation à la date du cycle requis', 
         ]);   
     }


        //   if ($user->id != $user->$participation->user_id)
        //  {
        //     // dd($user->id);
        //     dd( $user->$participation->user_id);
        //    return response()->json([
        //          'statut_code'=> false,
        //          'statut_message'=> 'vous n\'êtes pas participant qui doit effectuer le paiement à cette tontine', 
        //      ]);
        //  }
        $participants = ParticipationTontine::where('user_id', $user->id)
        ->where('tontine_id', $tontine->id)
        ->where('statutParticipation', 'accepte')
        ->get(); 

// dd($participants);
foreach ($participants as $participant) {
    // dd($participant->user_id);
    if ($user->id != $participant->user_id) {
       
        return response()->json([
            'statut_code' => false,
            'statut_message' => 'vous n\'êtes pas participant qui doit effectuer le paiement à cette tontine', 
        ]);
    }

        $cotisationExistant = CotisationTontine::where('gestion_cycle_id', $gestionCycles->id)
        ->where('participation_Tontine_id', $participant->id)
        ->first();

        if ($cotisationExistant) {
        return response()->json([
        'statut_code' => false,
        'statut_message' => 'Vous avez déjà effectué un paiement pour ce cycle.',
        ]);
        }


         $cotisationExistante = Payment::where('gestion_cycle_id', $gestionCycles->id)
         ->where('participation_Tontine_id', $participant->id)

         ->first();

         if ($cotisationExistante) {
         return response()->json([
         'statut_code' => false,
         'statut_message' => 'Vous avez déjà effectué un paiement pour ce cycle.',
         ]);
         }
        $montantDejaCotise = CotisationTontine::where('gestion_cycle_id', $gestionCycle->id)
        ->sum('montant_paiement');
    //    dd($montantDejaCotise);
           $cotisations = new CotisationTontine();

     
         $cotisations ->date_paiement = $date;
         $cotisations->montant_paiement =intval($montantTontine);
        $cotisations->gestion_cycle_id =$gestionCycles->id;
        $cotisations->participation_Tontine_id = $participant->id;
        $cotisations->montant_a_gagner=$montantDejaCotise + intval($montantTontine)  ; 

         $cotisations->save();
        
         $cotisations->update(['statutCotisation'=>'cotise']);

  
        //  return response()->json([
        //      'statut_code'=> 200,
        //      'statut_message'=> 'votre cotisation a été effectué avec succés',
        //      'data'=>$cotisations                  
        //      ]);
        
             $paymentController = new PaymentController();
             $participation_Tontine_id = $participant->id;
             $price =$montantTontine;
             $gestion_cycle_id = $gestionCycles->id;

            //  return view('index',[
            //             'price' =>$montantTontine,
            //             'gestion_cycle_id' => $gestionCycles->id,
            //             'participation_Tontine_id' =>  $participant->id
            //         ]);
            
                   
             return view('index', compact( 'price', 'gestion_cycle_id','participation_Tontine_id'));

            // return response()->json([
            //     "url"=>"http://localhost:8000/api/vue?price=$price&gestion_cycle_id=$gestion_cycle_id&participation_Tontine_id=$participation_Tontine_id"
            // ]);
        

           
            // $gestionCycles->update(['statut'=>'termine']);
}
}

 }
    
    

    // public function faireTirage(GestionCycle $nonGagnants)
    // {
    //     // Récupérer la liste des utilisateurs non-gagnants
    //     $nonGagnants = GestionCycle::where(['statutTirage'=>'pasgagnant'])->get();

    //     if ($nonGagnants->isEmpty()) {
    //         return response()->json(['message' => 'Il n\'y a pas de non-gagnants pour le moment.'], 404);
    //     }

    //     // Effectuer le tirage au sort
    //     $gagnant = $nonGagnants->random();

    //     // Mettre à jour le statut du gagnant dans la base de données
    //     $gagnant->update(['statutTirage' => 'gagnant']);

    //     // Notifier le gagnant (vous devrez implémenter la logique de notification)
    //     $this->notifierGagnant($gagnant);

    //     return response()->json(['gagnant' => $gagnant, 'message' => 'Le tirage au sort a été effectué avec succès.']);
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

            
            

    
           
            return response()->json([
                'statut_code'=> 200,
                'statut_message'=>'la liste de tous les cotisations',
                'data'=>$participation,
            ]);
        }
        


        

        public function listerCotisationsEtUtilisateursNonCotises()
        {
            // Récupérer tous les cycles
            $cycles = GestionCycle::all();
            
            $result = [];
            
            foreach ($cycles as $cycle) {
                // Récupérer toutes les cotisations pour ce cycle
                $cotisations = CotisationTontine::where('gestion_cycle_id', $cycle->id)->get();
                
                // Récupérer tous les utilisateurs ayant une participation à la tontine mais qui n'ont pas cotisé pour ce cycle
                $utilisateursNonCotises = ParticipationTontine::where('tontine_id', $cycle->tontine_id)
                    ->whereNotIn('user_id', $cotisations->pluck('participation_Tontine_id')->toArray())
                    ->get();
                
                $result[] = [
                    'cycle' => $cycle,
                    'cotisations' => $cotisations,
                    'utilisateurs_non_cotises' => $utilisateursNonCotises,
                ];
            }
            
            return $result;
        }
  

        public function utilisateursCotise(GestionCycle $cycle)
        {
            $cycles = GestionCycle::FindOrFail($cycle->id);
            
            $userCotiseParCycle = $cycles->CotisationTontines;
          return response()->json([
            'status_code'=>200,
            'status_message'=>'la liste des paiements par cycle',
            'data'=>$userCotiseParCycle
          ]);
        }

        

        public function utilisateursNonCotise(GestionCycle $cycle)
        {
            $cycles = GestionCycle::FindOrFail($cycle->id);
            $userCotiseParCycles = $cycles->tontine;

            foreach($userCotiseParCycles as $userCotiseParCycle)
            {
                $userCotiseParCycle->participationTontines->cotisationTontines;
            }

            return response()->json([
                'status_code'=>200,
                'status_message'=>'la liste des paiements par cycle',
                'data'=>$userCotiseParCycle
              ]);
        }
        
}
