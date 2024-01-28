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
  
    public function demandeCreationTontine(TontineCreateRequest $request, User $user)
    {
        try {
          
       
        $tontines = new Tontine();
        $tontines->user_id = auth()->user()->id;
        $tontines->libelle = $request->libelle;
        $tontines->description = $request->description;
        $tontines->montant = $request->montant;
        // $tontines->nombre_participant = $request->nombre_participant;
        $tontines->regles = $request->regles;
        $tontines->date_de_debut = $request->date_de_debut;
        $tontines->periode = $request->periode;
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
        // $tontines->nombre_participant = $request->nombre_participant;
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



    public function CreationTontineAccepted(Tontine $tontines,User $user)
{ 

    //dd($tontines->user->role);
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

public function CreationTontineRefuse(Tontine $tontines)
{ 

    //dd($tontines->user->role);
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



public function tontineAccepte(Tontine $tontines)
{
    $tontines = Tontine ::all()->where('statutTontine','accepte');
    return response()->json([
        'status_code'=>200,
        'status_message'=>'la liste des tontines acceptées',
        'data'=>$tontines,
    ]);
}

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
