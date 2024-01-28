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
    public function demandeParticipationTontine(ParticipationCreateRequest $request)
    {
        //dd(auth()->user()->id);
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
