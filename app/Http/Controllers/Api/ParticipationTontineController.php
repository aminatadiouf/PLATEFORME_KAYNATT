<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Tontine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ParticipationTontine;
use App\Http\Requests\ParticipationCreateRequest;
use App\Notifications\RefuseParticipationTontine;
use App\Notifications\AcceptedParticipationTontine;
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
 */


class ParticipationTontineController extends Controller
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
 * Demander à participer à une tontine.
 *
 * @param  \App\Http\Requests\ParticipationCreateRequest  $request
 * @return \Illuminate\Http\Response
 *
 * @OA\Post(
 *     path="/auth/ParticiperTontine",
 *     summary="Demander à participer à une tontine",
 *     tags={"ParticipationTontines"},
 *     security={{"jwt_token":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"tontine_id", "date"},
 *             @OA\Property(property="tontine_id", type="integer", description="ID de la tontine à laquelle participer"),
 *             @OA\Property(property="date", type="string", format="date", description="Date de participation")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Demande de participation à la tontine effectuée avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="status_message", type="string", example="Votre demande de participation sera approuvée ou déclinée par le créateur de cette tontine. Vous recevrez une notification bientôt."),
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Vous avez déjà participé à cette tontine",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="status_message", type="string", example="Vous avez déjà participé à cette tontine.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur"
 *     )
 * )
 */


    public function demandeParticipationTontine(ParticipationCreateRequest $request)
    {
        try {
            //code...
     
        $participations = new ParticipationTontine();
        $participations->user_id = auth()->user()->id;
        $participations->tontine_id = $request->tontine_id;
        $participations->statutParticipation = 'en_attente';
        $participations->date = $request->date;

        if($participations->user_id)
        {
            
        return response()->json([
            "status" => false,
            'status_message'=> 'Vous avez déjà participé à cette tontine.',
        ]);
        }
   
        $participations->save();

        return response()->json([
            "status" => true,
            'status_message'=> 'votre de demande de participation sera approuvé ou décliné par le créateur de ce tontine. Vous allez recevroir une notification bientôt ',
            "data"=>$participations,
            ]);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function allParticipation(ParticipationTontine $participationTontines)
    {
        try{

        
        return response()->json([
            'status_code'=>200,
            'status_message'=>'la liste de tous les tontines',
            'data'=>$participationTontines :: all(),
        ]);
          
        } catch (Exception $e) {
            return response()->json($e);
        }

    }

        public function accepteParticipation(ParticipationTontine $participeTontine)
        {
            try {
                if ($participeTontine->statutParticipation === 'accepte') {
                    return response()->json([ 
                        "status" => false,
                        "message" => "la tontine a déjà été acceptée "
                    ]);
                }
            
        
           
               
            
              $participeUser = $participeTontine->update(['statutParticipation' => 'accepte']);
              $participeUser = $participeTontine->user;

              $participeUser->notify(new AcceptedParticipationTontine());
        
    
              return response()->json([ 
                  "status" => true,
                  "message" => "la demande de création de tontine a été acceptée",
                  "data"=>$participeTontine
              ]);
          } catch (Exception $e) {
              return response()->json($e);
          }
        }
   

        public function refuseParticipation(ParticipationTontine $participeTontineRefuse)
        {
            try {
                if ($participeTontineRefuse->statutParticipation === 'accepte') {
                    return response()->json([ 
                        "status" => false,
                        "message" => "la tontine a déjà été acceptée "
                    ]);
                }
            
        
           
            
               
            
              $participeUser = $participeTontineRefuse->update(['statutParticipation' => 'accepte']);
              $participeUser = $participeTontineRefuse->user;

              $participeUser->notify(new RefuseParticipationTontine());
        
    
              return response()->json([ 
                  "status" => true,
                  "message" => "la demande de création de tontine a été acceptée",
                  "data"=>$participeTontineRefuse
              ]);
          } catch (Exception $e) {
              return response()->json($e);
          }
        }
   
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
//Si j'ai accepté la demande

    // if($participations->user->role ==='createur_tontine')
    // {
    //     $participations->user->update(['role' => 'createur_et_participant_tontine']);
    // }


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



//     $tontine =Tontine :: FindOrFail($tontine->id);
            
//     $cotisations = $tontine->participacipationTontines ;
// Pour lister la participation par tontine

}
