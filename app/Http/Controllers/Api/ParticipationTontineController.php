<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Tontine;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use App\Models\ParticipationTontine;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ParticipationCreateRequest;
use App\Models\GestionCycle;
use App\Notifications\RefuseParticipationTontine;
use App\Notifications\AcceptedParticipationTontine;


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
 *  *     security={{"bearerAuth":{}}},

 *    
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
            $tontine = Tontine::findOrFail($request->tontine_id);

            if($tontine->participationTontines()->count()==$tontine->nombre_participant)
            {
                return response()->json([
                    "status" => false,
                    'status_message' => 'Le nombre de participants de cette tontine a été atteint.',
                ]);
            }

            if($tontine->etat ==='en_cours'){
                
                return response()->json([
                    "status" => false,
                    'status_message' => 'vous ne pouvez pas vous inscrire à cette tontine,elle est en cours.',
                ]);
            }
     
        $participations = new ParticipationTontine();
        $participations->user_id = auth()->user()->id;
        $participations->tontine_id = $request->tontine_id;
        $participations->statutParticipation = 'en_attente';
        $participations->date = $request->date;



        $existingParticipation = ParticipationTontine::where('user_id', auth()->user()->id)
        ->where('tontine_id', $request->tontine_id)
        ->first();
        if($existingParticipation)
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


/**
 * Accepte la participation d'un utilisateur à une tontine en tant que créateur de la tontine.
 *
 * @param  ParticipationTontine $participationTontines
 * @return \Illuminate\Http\JsonResponse
 *
 * @OA\Post(
 *     path="/createur_tontine/AcceptedParticipationUser/{participationTontines}",
 *     summary="Accepter la participation d'un utilisateur à une tontine",
 *     description="Accepter la participation d'un utilisateur à une tontine par le créateur de la tontine.",
 *     operationId="accepteParticipation",
 *  security={{"bearerAuth":{}}},
 *     tags={"CreateurTontine"},
 *     @OA\Parameter(
 *         name="participationTontines",
 *         in="path",
 *         description="L'ID de la participation à la tontine à accepter",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Succès - La demande de participation à la tontine a été acceptée",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 description="Indique si l'opération a réussi ou non"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message indiquant le résultat de l'opération"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - L'utilisateur n'est pas le créateur de la tontine",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 description="Indique si l'opération a réussi ou non"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message d'erreur indiquant l'accès non autorisé"
 *             )
 *         )
 *     ),
 *    
 *   
 * )
 */



        public function accepteParticipation(ParticipationTontine $participationTontines)
        {
            try {

                $participationTontine = ParticipationTontine::FindOrFail($participationTontines->id);
                $userCreateur = Auth::user();
              

                if ($userCreateur->id !== $participationTontine->tontine->user_id) {
                    
                    return response()->json([
                        'status' => false,
                        'message' => 'vous êtes pas le créateur de cette tontine',
                    ]);
                }
                
                if ($participationTontine->statutParticipation === 'accepte') {
                    return response()->json([ 
                        "status" => false,
                        "message" => "la tontine a déjà été acceptée "
                    ]);
                }

              $participationTontine->update(['statutParticipation' => 'accepte']);
              $participeUser = User::find($participationTontine->user_id);

                

              $participeUser->notify(new AcceptedParticipationTontine());
        
             
              return response()->json([ 
                  "status" => true,
                  "message" => "la demande de création de tontine a été acceptée",
                //   "data"=>$participationTontine
              ]);
          } catch (Exception $e) {
              return response()->json($e);
          }
        }
   


/**
 * Refuse la participation d'un utilisateur à une tontine en tant que créateur de la tontine.
 *
 * @param  ParticipationTontine $participationTontines
 * @return \Illuminate\Http\JsonResponse
 *
 * @OA\Post(
 *     path="/createur_tontine/RefuseParticipationUser/{participationTontines}",
 *     summary="Refuser la participation d'un utilisateur à une tontine",
 *     description="Refuser la participation d'un utilisateur à une tontine par le créateur de la tontine.",
 *     operationId="refuseParticipation",
 *  security={{"bearerAuth":{}}},
 *     tags={"CreateurTontine"},
 *     @OA\Parameter(
 *         name="participationTontines",
 *         in="path",
 *         description="L'ID de la participation à la tontine à accepter",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Succès - La demande de participation à la tontine a été acceptée",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 description="Indique si l'opération a réussi ou non"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message indiquant le résultat de l'opération"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - L'utilisateur n'est pas le créateur de la tontine",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 description="Indique si l'opération a réussi ou non"
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message d'erreur indiquant l'accès non autorisé"
 *             )
 *         )
 *     ),
 *    
 *   
 * )
 */

        
        public function refuseParticipation(ParticipationTontine $participationTontines)
        {
            try {



                $participationTontine = ParticipationTontine::FindOrFail($participationTontines->id);
                $userCreateur = Auth::user();
              

                if ($userCreateur->id !== $participationTontine->tontine->user_id) {
                    
                    return response()->json([
                        'status' => false,
                        'message' => 'vous êtes pas le créateur de cette tontine',
                    ]);
                }

                if ($participationTontine->statutParticipation === 'refuse') {
                    return response()->json([ 
                        "status" => false,
                        "message" => "la demande de participation à la tontine a déjà été refusée "
                    ]);
                }
              $participationTontine->update(['statutParticipation' => 'accepte']);
                $participeUser = User::find($participationTontine->user_id);
  
                  
              $participeUser->notify(new RefuseParticipationTontine());
        
    
              return response()->json([ 
                  "status" => true,
                  "message" => "la demande de participation à la tontine a été refusée",
                
              ]);
          } catch (Exception $e) {
              return response()->json($e);
          }
        }

        public function listeUserNonGagnant()
        {
            $participations = ParticipationTontine::where('statutTirage','Nongagnant')->get();
            return response()->json([
                        'status_code'=>200,
                        'status_message'=>'la liste des participants non gagnés',
                        'data'=>$participations
            ]);
        }
   
  

public function allParticipationParTontine(Tontine $tontine)
{


    $tontines =Tontine :: FindOrFail($tontine->id);
            
    $participations = $tontines->participationTontines ;

    return response()->json([
        'status_code'=>200,
        'status_message'=>'la liste de tous les participations de cette tontine',
        'data'=> $participations
    ]);
}

/**
 * Récupérer la liste des participations en attente pour une tontine donnée.
 *
 * @param  Tontine $tontine
 * @return \Illuminate\Http\JsonResponse
 *
 * @OA\Get(
 *     path="/createur_tontine/ListeparticipationEnattentePartontine/{tontine}",
 *     summary="Liste des participations en attente pour une tontine",
 *     description="Récupère la liste des participations en attente pour une tontine donnée, accessible uniquement par le créateur de la tontine.",
 *     operationId="listeParticipationsEnAttente",
 *     security={{"bearerAuth":{}}},
 *     tags={"CreateurTontine"},
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
 *         description="Succès - La liste des participations en attente a été récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status_code",
 *                 type="integer",
 *                 example=200
 *             ),
 *             @OA\Property(
 *                 property="status_message",
 *                 type="string",
 *                 example="la liste de tous les cotisations de cette tontine"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(
 *                         property="id",
 *                         type="integer",
 *                         example=1
 *                     ),
 *                     @OA\Property(
 *                         property="statutParticipation",
 *                         type="string",
 *                         example="en_attente"
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - L'utilisateur n'est pas le créateur de la tontine"
 *     )
 * )
 */



public function participationTontineEnAttente(Tontine $tontine)
{
    $tontines =Tontine :: FindOrFail($tontine->id);
    $user = Auth::user();

    if ($user->id !== $tontine->user_id) {
        return response()->json([
            'status'=>false,
            'status_message'=>'vous êtes pas le créateur de cette tontine '
        ]);

    }
    $participations = $tontines->participationTontines()->where('statutParticipation','en_attente')->get() ;


    return response()->json([
        'status_code'=>200,
        'status_message'=>'la liste de tous les cotisations de cette tontine',
        'data'=> $participations
    ]);


}


/**
 * Récupérer la liste des participations acceptées pour une tontine donnée.
 *
 * @param  Tontine $tontine
 * @return \Illuminate\Http\JsonResponse
 *
 * @OA\Get(
 *     path="/auth/ListeparticipationAcceptePartontine/{tontine}",
 *     summary="Liste des participations acceptées pour une tontine",
 *     description="Récupère la liste des participations acceptées pour une tontine donnée, accessible uniquement par le créateur de la tontine.",
 *     operationId="listeParticipationsAcceptees",
 *     security={{"bearerAuth":{}}},
 *     tags={"ParticipationTontines"},
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
 *         description="Succès - La liste des participations acceptées a été récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status_code",
 *                 type="integer",
 *                 example=200
 *             ),
 *             @OA\Property(
 *                 property="status_message",
 *                 type="string",
 *                 example="la liste de tous les cotisations de cette tontine"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(
 *                         property="id",
 *                         type="integer",
 *                         example=1
 *                     ),
 *                     @OA\Property(
 *                         property="statutParticipation",
 *                         type="string",
 *                         example="accepte"
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé - L'utilisateur n'est pas le créateur de la tontine"
 *     )
 * )
 */



public function participationTontineAccepte(Tontine $tontine)
{
    $tontines =Tontine :: FindOrFail($tontine->id);
    // $user = Auth::user();

    // if ($user->id !== $tontine->user_id) {
    //     return response()->json([
    //         'status'=>false,
    //         'status_message'=>'vous êtes pas le créateur de cette tontine '
    //     ]);

    // }
    $participations = $tontines->participationTontines()->where('statutParticipation','accepte')->get() ;

    $user = [];
    foreach($participations as $participation){
        $user []=[
        'id'=>$participation->id,
        'user_id'=>$participation->user_id,
        'name'=>$participation->user->name,
        'email'=>$participation->user->email,
        'password'=>$participation->user->password,
        'adresse'=>$participation->user->adresse,
        'telephone'=>$participation->user->telephone,
        'num_carte_d_identite'=>$participation->user->num_carte_d_identite,
        'telephone_d_un_proche'=>$participation->user->telephone_d_un_proche,
        'role'=>$participation->user->role,
        'tontine_id'=>$participation->tontine_id,
        ];

    }


    return response()->json([
        'status_code'=>200,
        'status_message'=>'la liste de tous les cotisations de cette tontine',
        'data'=> $user
    ]);


}






/**
 * Effectue un tirage dans une tontine pour désigner un participant gagnant.
 *
 * @param \App\Models\Tontine $tontines Instance de la tontine pour laquelle le tirage doit être effectué.
 * @return \Illuminate\Http\JsonResponse
 *
 * @OA\Post(
 *     path="/createur_tontine/faireTirage/{gestionCycle}",
 *     summary="Effectuer un tirage dans une tontine",
 *     description="Effectue un tirage dans une tontine pour désigner un participant gagnant.",
 *     operationId="EffectuerTirage",
 *     tags={"Tirage"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="tontines",
 *         in="path",
 *         description="ID de la tontine",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Succès - Le tirage a été effectué avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="statut_code",
 *                 type="integer",
 *                 example=200
 *             )
 *            
 *                     
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête incorrecte - L'utilisateur n'est pas le créateur de la tontine"
 *     )
 * )
 */






public function EffectuerTirage(GestionCycle $gestionCycle)
{
    $gestionCycles =GestionCycle::FindOrFail($gestionCycle->id);
              
    $tontines = $gestionCycles->tontine()->get();
    if ($gestionCycles->statut != 'a_venir') {
        return response()->json([
            'status' => false,
            'status_message' => 'Le tirage est déjà  effectué pour ce cycle.'
        ]);
    }

// dd($tontine);

foreach($tontines as $tontine){
    $user = Auth::user();
    

    



        if($tontine->etat != 'en_cours') { 
           
               
                return response()->json([
                    'status'=>false,
                    'status_message'=>'la tontine n\'est pas en cours'
                ]);
            }
            if($user->id != $tontine->user_id)
            {
                return response()->json([
                    'status'=>false,
                    'status_message'=>'vous êtes pas le créateur de cette tontine '
                ]);
            }

            $participantsRestants = $tontine->participationTontines()
            ->where('statutTirage', 'pasgagnant')
            ->where('statutParticipation', 'accepte')
            ->count();
    // dd($participantsRestants);
        if ($participantsRestants == 0) {
            $tontine->etat = 'termine';
            $tontine->save();
    
            return response()->json([
                'status' => false,
                'status_message' => 'Tous les utilisateurs ont été tirés, la tontine est terminée.'
            ]);
        }
    
      
         
    $participantGagnant =[];
    
        // foreach($cycles as $cycle){
        // if ($cycle ->nombre_de_cycle !=1 && now()->isSameDay($cycle->date_cycle))
        // {
            //dd($cycle ->nombre_de_cycle !=1);


    
        $participantTontine = $tontine->participationTontines()
        ->where('statutTirage','pasgagnant')
        ->where('statutParticipation','accepte')
        ->get();
        if($participantTontine->count() > 0 )
                {
                    
                $gagnant = $participantTontine->random();
                $gagnant->statutTirage = 'gagnant';
                    $gagnant->save(); 
                    $participantGagnant[]=$gagnant;
                // break;
                $gestionCycles->statut ='termine';
        $gestionCycles->save();
        }

      
    }
           
        // } 
      
    // }
           
                return response()->json([
                    'statut_code'=> 200,
                    'statut_message'=> 'l\'utilisateur gagnant est',
                    'data'=>$participantGagnant
                ]);
    
 
}






}
