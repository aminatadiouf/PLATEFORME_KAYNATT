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
 * Gérer les cycles d'une tontine.
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
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données requises pour gérer les cycles de la tontine",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="statut",
 *                 type="string",
 *                 description="Statut des cycles"
 *             )
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
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - L'utilisateur n'est pas le créateur de la tontine"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête incorrecte - La tontine n'est pas dans un état accepté"
 *     )
 * )
 */




    public function gestionCycle(Request $request,Tontine $tontine)
    {

        $cyclesList = []; 
       $datesList = [];
           
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



    if($tontine->statutTontine === 'accepte')
   {  
       For($i=1; $i <=$nbre_participantTontine + 1; $i++)
        {
            
           
            $cycles = new GestionCycle();
            $cycles -> tontine_id = $tontine->id;
            $cycles->date_cycle = carbon::now()->addDays($duree * ($i - 1));
            $cycles ->nombre_de_cycle = $i;
            $cycles->statut = 'a_venir';

          
    foreach ($participationTontines as $participationTontine) {
        $participantsList[] = $participationTontine->toArray(); 
    }
            $tontine->update(['etat'=>'en_cours']);
if($cycles)
{
    return response()->json([
        'status_code'=> false,
        'status_message'=>'cette tontine a déjà ses cycles'
    ]);
    
}
            $cycles->save();

            $cyclesList[] = $cycles->fresh()->toArray(); 
            $datesList[] = $cycles->date_cycle->format('Y-m-d');


        } 
    

        return response()->json([
            'status_code'=>200,
            'status_message'=>'les cycles du tontine',
            'cycles' => $cyclesList, 
            'dates' => $datesList,
            'nombre_participants'=>$participantsList
        ]);
    
    } else {
        return response()->json([
            'status_code'=> false,
            'status_message'=>'vous ne pouvez pas effectuer cette action, la tontine n\'est pas encore accepte'
        ]);
    }


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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
