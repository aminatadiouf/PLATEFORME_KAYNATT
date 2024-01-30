<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Tontine;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditTontineRequest;
use App\Http\Requests\TontineCreateRequest;
use App\Notifications\RefuseDemandeTontine;
use App\Notifications\AccepteDemandeTontine;
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




class TontineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return 'salut';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(TontineCreateRequest $request )
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

     /**
 * Demander la création d'une tontine.
 *
 * @param  \App\Http\Requests\TontineCreateRequest  $request
 * @param  \App\Models\User  $user
 * @return \Illuminate\Http\Response
 *
 * @OA\Post(
 *     path="/auth/ajouterTontine",
 *     summary="Demande de creation d'une tontine",
 *     tags={"Tontines"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="libelle", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="montant", type="number"),
 *             @OA\Property(property="regles", type="string"),
 *             @OA\Property(property="date_de_debut", type="string", format="date"),
 *             @OA\Property(property="periode", type="string"),
 *              @OA\Property(property="etat", type="string", example="en_attente"),
 *             @OA\Property(property="statutTontine", type="string", example="en_attente"),
 *  *           @OA\Property(property="nombre_participant", type="integer"),

 *         
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Demande de création de tontine soumise avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status_code", type="string", example="200"),
 *             @OA\Property(property="status_message", type="string", example="Votre demande de création sera approuvée ou déclinée par l'administrateur de ce site. Vous recevrez une notification"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur",
 *     )
 * )
 */

  
    public function demandeCreationTontine(TontineCreateRequest $request, User $user)
    {
        try {
          
       
        $tontines = new Tontine();
        $tontines->user_id = auth()->user()->id;
        $tontines->libelle = $request->libelle;
        $tontines->description = $request->description;
        $tontines->montant = $request->montant;
        $tontines->regles = $request->regles;
        $tontines->date_de_debut = $request->date_de_debut;
        $tontines->periode = $request->periode;
        $tontines->nombre_participant = $request->nombre_participant;

        $tontines->etat = 'en_attente';
        $tontines->statutTontine = 'en_attente';

        $tontines->save();

     return response()->json([
             'status_code'=> '200',
            'status_message'=> 'votre de demande de création sera approuvé ou décliné par l\'administrateur de ce site. Vous recevrez une notification ',
            'data'=>$tontines
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

/**
 * Obtenir la liste de toutes les tontines.
 *
 * @return \Illuminate\Http\Response
 *
 * @OA\Get(
 *     path="/ListeTontine",
 *     summary=" la liste de toutes les tontines",
 *     tags={"Tontines"},
 *     @OA\Response(
 *         response=200,
 *         description="Liste de toutes les tontines récupérée avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status_code", type="integer", example=200),
 *             @OA\Property(property="status_message", type="string", example="La liste de tous les tontines")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur"
 *     )
 * )
 */


    public function tousLesTontines(Tontine $allTontine)
    {
        try {
         
            $allTontine = Tontine::all();

            return response()->json([
                'status_code'=>200,
                'status_message'=>'la liste de tous les tontines',
                'data'=>$allTontine,
            ]);
              
            } catch (Exception $e) {
                return response()->json($e);
            }
    }
/**
 * @OA\Get(
 *     path="/createur_tontine/ListeTontineparCreateur/{user}",
 *     summary="Obtenir la liste de toutes les tontines créées par un créateur_tontine",
 *     tags={"CreateurTontine"},
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         description="ID de l'utilisateur créateur des tontines",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste de toutes les tontines créées par l'utilisateur récupérée avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status_code", type="integer", example=200),
 *             @OA\Property(property="status_message", type="string", example="La liste de toutes les tontines créées par l'utilisateur")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Utilisateur non trouvé",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur",
 *     )
 * )
 */



    public function alltontineparcreateur(Tontine $tontine,User $user)
    {
       
        try {
         
            $createur = User::findOrFail($user->id);  
            $listetontineparcreateur=$createur->tontines()->get();
           

            return response()->json([
                'status_code'=>200,
                'status_message'=>'la liste de tous les tontines crée par un utilisater',
                'data'=> $listetontineparcreateur,
            ]);
              
            } catch (Exception $e) {
                return response()->json($e);
            }
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
    public function update(EditTontineRequest $request, Tontine $tontines)
    {
        try {
            
       

        $tontines->libelle = $request->libelle;
        $tontines->description = $request->description;
        $tontines->montant = $request->montant;
        $tontines->nombre_participant = $request->nombre_participant;
        $tontines->regles = $request->regles;
        $tontines->date_de_debut = $request->date_de_debut;
        $tontines->periode = $request->periode;
        $tontines->etat = $request->etat;
        $tontines->statutTontine = 'en_attente';


        $tontines->save();

        return response()->json([
             'status_code'=> '200',
            'status_message'=> 'les informations cooncernant la tontine ont été modifiés avec succés',
            'data'=>$tontines]
        );

    } catch (Exception $e) {
        return response()->json($e);
    }
    }

   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tontine $tontines)
    {
        try {
           
             if($tontines){
                    $tontines->delete();
    
                    
                    return response()->json([
                        'status_code'=>200,
                        'status_message'=>'la tontine a été supprimée avec succés',
                        'data'=>$tontines
                    ]);
                }
            } catch (Exception $e) {
                return response()->json($e);
            }
              
    }

/**
 * Accepter la création d'une tontine.
 *
 * @param Tontine $tontines ID de la tontine à accepter
 * @return \Illuminate\Http\Response
 *
 * @OA\Post(
 *     path="AcceptedTontine/{tontines}",
 *     summary="Accepter la création d'une tontine et notifier le createur",
 *     tags={"Admins"},

 *     @OA\Parameter(
 *         name="tontines",
 *         in="path",
 *         required=true,
 *         description="ID de la tontine à accepter",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="La demande de création de tontine a été acceptée",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="La demande de création de tontine a été acceptée")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="La tontine a déjà été acceptée",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="La tontine a déjà été acceptée")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Tontine non trouvée",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur",
 *     )
 * )
 */


    public function CreationTontineAccepted(Tontine $tontines,User $user)
{ 

    try {
        if ($tontines->statutTontine === 'accepte') {
            return response()->json([ 
                "status" => false,
                "message" => "la tontine a déjà été acceptée "
            ]);
        }
    
       $tontines->update(['statutTontine' => 'accepte']);

   
        if($tontines->user->role === 'participant_tontine')

        
        {
            $tontines->user->update(['role' => 'createur_tontine']);
        }
       
    
        $createurTontine = $tontines->user; 
            $createurTontine->notify(new AccepteDemandeTontine());
        
    
        return response()->json([ 
            "status" => true,
            "message" => "la demande de création de tontine a été acceptée",
            "data"=>$tontines
        ]);
    } catch (Exception $e) {
        return response()->json($e);
    }
}


/**
 * Refuser la création d'une tontine.
 *
 * @param Tontine $tontine La tontine à refuser
 * @return \Illuminate\Http\Response
 *
 * @OA\Post(
 *     path="admin/RefuseTontine/{tontine}",
 *     summary="Refuser la création d'une tontine et notifier le createur ",
 *     tags={"Admins"},
  *     security={{"jwt_token":{}}},

 *     @OA\Parameter(
 *         name="tontine",
 *         in="path",
 *         required=true,
 *         description="ID de la tontine à refuser",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="La demande de création de tontine a été refusée",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="La demande de création de tontine a été refusée")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="La tontine a déjà été refusée",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="La tontine a déjà été refusée")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Tontine non trouvée",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur",
 *     )
 * )
 */
public function CreationTontineRefuse(Tontine $tontines)
{ 

    try {
        if ($tontines->statutTontine === 'refuse') {
            return response()->json([ 
                "status" => false,
                "message" => "la tontine a déjà été acceptée "
            ]);
        }
    
       $tontines->update(['statutTontine' => 'refuse']);

       $createurTontine = $tontines->user; 
            $createurTontine->notify(new RefuseDemandeTontine());

         return response()->json([ 
            "status" => true,
            "message" => "la demande de création de tontine a été refusée",
            "data"=>$tontines
        ]);
    } catch (Exception $e) {
        return response()->json($e);
    }
}



public function allParticipationParTontine(Tontine $tontines)
{
   
    try{
    $participationparTontine = Tontine :: FindOrFail($tontines->id);
        
    $participationTontines = $participationparTontine->participacipationTontines;
    
    return response()->json([
        'status_code'=>200,
        'status_message'=>'la liste de tous les tontines',
        'data'=>$participationTontines,
    ]);
      
    } catch (Exception $e) {
        return response()->json($e);
    }

}

public function allCotisationParTontine(Tontine $tontines)

{
    $tontine = Tontine ::FindOrFail($tontines->id);
    $cotisations = $tontine-> participationTontines;

    foreach($cotisations as $cotisation)
    {
        $cotisation->cotisationTontines;
    }
    return response()->json([
        'status_code'=>200,
        'status_message'=>'la liste de tous les tontines',
        'data'=>$cotisations,
    ]);
}

/**
 * Obtenir la liste de toutes les tontines acceptées.
 *
 * @return \Illuminate\Http\Response
 *
 * @OA\Get(
 *     path="admin/ListeTontineAccepte",
 *     summary="La liste de toutes les tontines acceptées",
 *     tags={"Admins"},
 *   security={{"jwt_token":{}}},
*     @OA\Response(
 *         response=200,
 *         description="Liste de toutes les tontines acceptées récupérée avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status_code", type="integer", example=200),
 *             @OA\Property(property="status_message", type="string", example="La liste de toutes les tontines acceptées")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur"
 *     )
 * )
 */

public function tontineAccepte(Tontine $tontines)
{
    $tontines = Tontine ::all()->where('statutTontine','accepte');
    return response()->json([
        'status_code'=>200,
        'status_message'=>'la liste des tontines acceptées',
        'data'=>$tontines,
    ]);
}


/**
 * Obtenir la liste de toutes les tontines en attente.
 *
 * @return \Illuminate\Http\Response
 *
 * @OA\Get(
 *     path="admin/ListeTontineEnAttente",
 *     summary="La liste de toutes les tontines en attente",
 *     tags={"Admins"},
 *     security={{"jwt_token":{}}},
*       @OA\Response(
 *         response=200,
 *         description="Liste de toutes les tontines en attente récupérée avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status_code", type="integer", example=200),
 *             @OA\Property(property="status_message", type="string", example="La liste de toutes les tontines en attente")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur"
 *     )
 * )
 */
public function tontineEnAttente(Tontine $tontines)
{
    $tontines = Tontine ::all()->where('statutTontine','en_attente');
    return response()->json([
        'status_code'=>200,
        'status_message'=>'la liste des tontines en attente',
        'data'=>$tontines,
    ]);
}
}
