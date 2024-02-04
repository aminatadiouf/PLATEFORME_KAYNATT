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
    
       $montantUser = $participationTontine->tontine->montant;

        if($request->montant_paiement != $montantUser )


        {
         return response()->json([
 
             'statut_code' => false,
             'statut_message' => 'Le montant de la cotisation est différent du montant de la tontine.',
         ]);
        }


        $cycles = $participationTontine->tontine->gestion_cycles()
        ->get();
           //dd($cycles);
            

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
                    'statut_message'=> 'l\'utilisateur connecté n\'est pas un participant de la tontine ',
                   
                ]);
            }
       if($participationTontine->statutParticipation !='accepte')
       {
        return response()->json([
            'statut_code'=> false,
            'statut_message'=> 'l\'utilisateur n\'est pas encore accepté à participer à la tontine  ',
           
        ]);
       }
       foreach ($cycles as $cycle) {
        $cotisationExistante = Payment::where('participation_Tontine_id', $participationTontine->id)
           ->where('gestion_cycle_id', $request->gestion_cycle_id)
            ->count();

        if ($cotisationExistante >0) {
            return response()->json([
                'statut_code' => false,
                'statut_message' => 'L\'utilisateur a déjà cotisé pour ce cycle de tontine.',
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
        $cotisations->gestion_cycle_id = $request->gestion_cycle_id;
 
       
 
          $cotisations->save();
          

          $participationTontine->update(['statutCotisation' => 'cotise']);

            $paymentController = new PaymentController();
        $participation_Tontine_id = $participationTontine->id;
        $price =$montantUser;
        $gestion_cycle_id = $request->gestion_cycle_id;
    
        return view('index', compact('participation_Tontine_id', 'price', 'gestion_cycle_id'));
   
       
      

     
     
 
      //  Effectuer le paiement
    }  

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


   


        public function listeCotisation()
        {
            return response()->json([
                'statut_code'=> 200,
                'statut_message'=> 'votre cotisation a été effectué avec succés',
                'data'=>CotisationTontine::all(),
            ]);
        }

        
    // public function userNonCotise(Tontine $tontine)
    // {
    //     $to
    // }


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
