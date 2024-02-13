<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Tontine;
use App\Models\GestionCycle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ParticipationTontine;
use Illuminate\Support\Facades\Auth;
use App\Notifications\RappelCotisation;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      title="Kaynatt API",
 *      version="1.0",
 *      description="Mon API"
 * )
 * 
 * @OA\Server(
 *      url="http://localhost:8000/api"
 * )
 

 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * )
 */


class GestionCycleController extends Controller
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
 * Gère les cycles d'une tontine.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \App\Models\Tontine  $tontine
 * @return \Illuminate\Http\JsonResponse
 *
 * @OA\Post(
 *     path="/createur_tontine/gererCycle/{tontine}",
 *     summary="Gérer les cycles d'une tontine",
 *     description="Crée et liste les cycles d'une tontine en fonction de sa période.",
 *     operationId="gestionCycle",
 *     tags={"CreateurTontine"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="tontine",
 *         in="path",
 *         description="ID de la tontine",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Succès - Les cycles de la tontine ont été gérés avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status_code",
 *                 type="integer",
 *                 example=200
 *             ),
 *             @OA\Property(
 *                 property="status_message",
 *                 type="string",
 *                 example="les cycles du tontine"
 *             ),
 *             @OA\Property(
 *                 property="cycles",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object"
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="dates",
 *                 type="array",
 *                 @OA\Items(
 *                     type="string",
 *                     example="2024-02-10"
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="nombre_participants",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - L'utilisateur n'est pas le créateur de la tontine"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête incorrecte - La tontine n'est pas dans un état accepté ou a déjà des cycles"
 *     )
 * )
 */



    public function gestionCycle(Request $request,Tontine $tontine)
    {

        $cyclesList = []; 
       $datesList = [];
       $participantsList=[];
           
        if ($tontine->periode === 'hebdomaire')
        {
            
                $duree = 7;
               
            
        }
        elseif ($tontine->periode === 'mensuel'){
           
            $duree = 30;

           
        }

        elseif ($tontine->periode === 'quotidien'){
            
             $duree = 1;

            
        }
            elseif ($tontine->periode === 'annuel'){
                 
                 $duree = 365;

                
            }


            $tontines = Tontine::findOrFail($tontine->id);
            $nbre_participantTontine = $tontines->participationTontines()
            ->where('statutParticipation','accepte')
            ->count(); 

            $participantsList = [];
            $participationTontines = $tontines->participationTontines()
            ->where('statutParticipation','accepte')->get();


       $user = Auth::user();
      
      
           if ($user->id !== $tontines->user_id) {
               return response()->json([
                   'status'=>false,
                   'status_message'=>'vous êtes pas le créateur de cette tontine,vous n\'êtes pas le créateur de cette tontine '
               ]);
       
           }
          
        //    $existingCyclesCount = GestionCycle::where('tontine_id', $tontine->id)->count();

        //    if ($existingCyclesCount > 0) {
        //        return response()->json([
        //            'status_code' => false,
        //            'status_message' => 'cette tontine a déjà ses cycles'
        //        ]);
        //    }    

if($tontine->statutTontine === 'en_attente'|| $tontine->statutTontine === 'refuse' )
{
    return response()->json([
        'status_code'=> false,
        'status_message'=>'vous ne pouvez pas effectuer cette action, la tontine n\'est pas encore accepte'
    ]);
}

   
     foreach ($participationTontines as $participationTontine) {
       For($i=1; $i <=$nbre_participantTontine + 1; $i++)
        {
            
           
            $cycles = new GestionCycle();
            $cycles -> tontine_id = $tontine->id;
            $cycles->date_cycle = carbon::now()->addDays($duree * ($i - 1));
            $cycles ->nombre_de_cycle = $i;
            $cycles->participation_Tontine_id=$participationTontine->id;
            $cycles->statut = 'a_venir';

            $cyclesList[] = $cycles->toArray(); 
            
            $datesList[] = $cycles->date_cycle->format('Y-m-d');
            $participantsList[] = $participationTontine->toArray();
            $cycles->save();
   }
  
}   

           

         

            $tontine->update(['etat'=>'en_cours']);
        
    

        return response()->json([
            'status_code'=>200,
            'status_message'=>'les cycles du tontine',
            'cycles' => $cyclesList, 
            'dates' => $datesList,
            'nombre_participants'=>$participantsList
        ]);
   
      
}

public function listeTontineGestionCycle(Tontine $tontine)
{
   $tontines = Tontine::FindOrFail($tontine->id);
   $gestionCycle = GestionCycle::where('tontine_id',$tontines->id);

   return response()->json([
    'status_code' => true,
    'status_message' => 'cette tontine a ses cycles',
    'data'=>$gestionCycle
]);

}


/**
 * @OA\Get(
 *     path="/participant_tontine/ListeCycleParparticipant/{participationTontine}",
 *     summary="Obtenir la liste des cycles pour une participation à une tontine donnée.",
 *    tags={"ParticipationTontines"},
 *     security={{ "jwt":{} }},
 *     @OA\Parameter(
 *         name="participationTontine",
 *         in="path",
 *         required=true,
 *         description="ID de la participation à la tontine",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Opération réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="status_code",
 *                 type="boolean",
 *                 example=true
 *             ),
 *             @OA\Property(
 *                 property="status_message",
 *                 type="string",
 *                 example="les cycles de l\'utilisateur"
 *             ),
 *         
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Non trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Non trouvé"
 *             )
 *         )
 *     )
 * )
 */



public function listeParticipantGestionCycle(ParticipationTontine $participationTontine)
{
   $participationTontines = ParticipationTontine::FindOrFail($participationTontine->id);
   $gestionCycle = GestionCycle::where('participation_Tontine_id',$participationTontines->id)
                                  ->where('tontine_id',$participationTontines->tontine_id)->get();

   return response()->json([
    'status_code' => true,
    'status_message' => 'les cycles de l\'utilisateur',
    'data'=>$gestionCycle
]);

}

// public function gestionCycleParUtilisateur(ParticipationTontine $participationTontines)
// {
//         $participationTontine = ParticipationTontine::FindOrFail($participationTontines->id);

//         $tontines = $participationTontine->tontine;
//        // dd($tontines);

//         if ($tontines->periode === 'hebdomaire')
//         {
            
//                 $duree = 7;
               
            
//         }
//         elseif ($tontines->periode === 'mensuel'){
           
//             $duree = 30;

           
//         }

//         elseif ($tontines->periode === 'quotidien'){
            
//              $duree = 1;

            
//         }
//             elseif ($tontines->periode === 'annuel'){
                 
//                  $duree = 365;

                
//             }

           
           
//             $nbre_participantTontine = $tontines->participationTontines()
//             ->where('statutParticipation','accepte')
//             ->count();

//             $user = Auth::user();
//     //   dd($user);
//       if($user->id!==$tontines->user_id)
//       {
//         return response()->json([
//             'status_code'=> false,
//             'status_message'=>'vous ne pouvez pas effectuer cette action, vous n\'êtes pas le créateur de cette tontine'
//         ]);
//       }
//     //   dd($tontines->user_id);     
//     $existingCyclesCount = GestionCycle::where('participation_Tontine_id', $participationTontine->id)->count();

//     if ($existingCyclesCount>0) {
//         return response()->json([
//             'status_code' => false,
//             'status_message' => 'Les cycles pour cette participation à la tontine ont déjà été créés.'
//         ]);
//     }    

//         if($participationTontine->statutParticipation === 'en_attente'|| $participationTontine->statutParticipation === 'refuse' )
//         {
//             return response()->json([
//                 'status_code'=> false,
//                 'status_message'=>'vous ne pouvez pas effectuer cette action, la tontine n\'est pas encore accepte'
//             ]);
//         }

//         $cyclesListe = []; 
//         $dateliste =[];
//     if($tontines->statutTontine === 'accepte')
//     {  
       
//         For($i=1; $i <=$nbre_participantTontine + 1; $i++)
//          {
             
            
//              $cycles = new GestionCycle();
//              $cycles -> tontine_id = $tontines->id;
//              $cycles->date_cycle = carbon::now()->addDays($duree * ($i - 1));
//              $cycles ->nombre_de_cycle = $i;
//              $cycles->participation_Tontine_id=$participationTontine->id;
//              $cycles->statut = 'a_venir';

//              $cycles->save(); 

//              $cyclesListe[]=$cycles;
//              $dateliste []= $cycles->date_cycle->format('Y-m-d');
//         }
//         $tontines->update(['etat' => 'en_cours']);

  
//         return response()->json([
//             'status_code'=>200,
//             'status_message'=>'les cycles de l\'utilisateur',
//             'data' => $cyclesListe, 
//             'dates' => $dateliste,
//         ]);
        
//     // }else{
//     //     return response()->json([
//     //         'status_code' => false,
//     //         'status_message' => 'Une erreur est survenue lors du traitement de la tontine.'
//     //     ]);
//     // }
      
// }




// }










/**
 * @OA\Get(
 *     path="/auth/listeCycle/{tontine}",
 *     summary="Liste des cycles pour une tontine spécifique",
 *     tags={"CreateurTontine"},
 *  security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="tontine",
 *         in="path",
 *         description="ID de la tontine",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="La liste des cycles pour la tontine spécifiée",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="status_code",
 *                 type="integer",
 *                 example=200
 *             ),
 *             @OA\Property(
 *                 property="status_message",
 *                 type="string",
 *                 example="la liste des cycles pour chaque tontine"
 *             ),
 *            
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="La tontine spécifiée n'existe pas"
 *     )
 * )
 */

        public function listeCycle(Tontine $tontine)
        {
            $tontines = Tontine::FindOrFail($tontine->id);
            $cycle = $tontines->gestion_cycles()->get();
            return response()->json([
                'status_code'=>200,
                'status_message'=>'la liste des cycles pour chaque tontine',
                'data'=>$cycle
            ]);
        }


    public function notificationCotisation(GestionCycle $gestion_cycles,Tontine $tontines)
    {
       
        $tontine = Tontine::FindOrFail($tontines->id);
      
        $participationTontines =$tontine->participationTontines()->Where('statutParticipation','accepte')
        ->get();
        
       
       
        $gestions= $tontine->gestion_cycles;
       
        foreach($gestions as $gestion)
        {
            $dateNotification = Carbon::parse($gestion->date_cycle)->subDays(5);

            
                if($dateNotification)
                {
                    foreach($participationTontines as $participationTontine)
                   {
                     $participationTontine->user->notify(new RappelCotisation);
                    }
                }
            
        }
        return response()->json([
            'status_code'=>200,
            'status_message'=>'la tontine est à  5 jours',
        ]);
    }

        public function listeUserparCycle(GestionCycle $cycle)
        {
            $cycles = GestionCycle::FindOrFail($cycle->id);

           
          $tontines = $cycles->tontine;
          foreach($tontines as $tontine)
         { 
            $participationTontine = $tontine->participationTontines;
          dd($participationTontine);
        }
          return response()->json([
            'status_code'=>200,
            'status_message'=>'la liste des paiements par cycle',
            'data'=>$participationTontine
          ]);
        }

    
    //     public function tirage($nombreCycle)
    //     {
            
    // $cycles = GestionCycle::where('nombre_de_cycle', $nombreCycle)->get();
    
   
       

  
    //         return response()->json([
    //             'statut_code'=> 200,
    //                 'statut_message'=> 'liste participation de cette cycle',
    //                 'data'=>$cycles  
    //         ]);
    //     }
       


    public function tirage(Tontine $tontine, GestionCycle $gestionCycle)
    {
        $tontines = Tontine::FindOrFail($tontine->id);

        $gestionCycle = GestionCycle::where('tontine_id', $tontines->id)
        ->where('statut', 'a_venir')
        ->get();
        dd($gestionCycle);
        $participantGagnant =[];
        $participants = $tontines->participationTontines()
        ->where('statutTirage','pasgagnant')
        ->where('statutParticipation','accepte')
        ->get();

       

      
        if($participants->count() > 0)
                {
                $gagnant = $participants->random();
                $gagnant->statutTirage = 'gagnant';
                    $gagnant->save(); 
                    $participantGagnant[]=$gagnant;
            }

              
            
           
                return response()->json([
                    'statut_code'=> 200,
                    'statut_message'=> 'l\'utilisateur gagnant est',
                    'data'=>$participantGagnant
                ]);
    }
    }  
  
