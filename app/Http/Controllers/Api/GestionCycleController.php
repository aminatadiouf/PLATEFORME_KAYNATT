<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Tontine;
use App\Models\GestionCycle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ParticipationTontine;
use App\Notifications\RappelCotisation;

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

//      public function gererecheance($id)
//      {
//         $tontine = Tontine::findOrFail($id);
//  //dd($tontine->id);
//         $echeance = DB::table('echeances')->where('id_tontine','=',$id)->first();
//  //dd ($echeance);
 
//                  if($tontine->periodicite == 'hebdomadaire')
//                  {
//                      $periodicite = 7;
//                  }
//                  elseif($tontine->periodicite == 'mensuelle')
//                  {
//                      $periodicite = 30;
//                  }
//                  elseif($tontine->periodicite == 'journalier')
//                  {
//                      $periodicite = 1;
//                  }
//                  elseif($tontine->periodicite == 'annuelle')
//                  {
//                      $periodicite = 365;
//                  }
 
//                  $date_echeance =Carbon::parse($tontine->dateDeb);
 
//                  if(!$echeance)
//                  {
//                      for($i=1 ; $i<=$tontine->nb_echeance ; $i++ )
//                      {
 
 
//                        $echeance = new Echeance;
//                        $echeance->id_tontine = $tontine->id;
 
//      $echeance->id_tontine = $tontine->id;
//                        $echeance->date=$date_echeance;
//                        $echeance->numero = $i;
//                        $echeance->save();
 
//                        $date_echeance = $date_echeance->addDay($periodicite) ;
//                      }
 
//                      toastr()->success('Echeance generer avec succés');
//                      return back();
//                  }
//                  else{
 
//                      toastr()->error('Vous avez deja genere l\'echeance');
//                      return back();
 
//                  }
//      }
/*
  $table->integer('nombre_de_cycle');
            $table->date('date_cycle');
            $table->enum('statut',['termine','a_venir']);

*/

/*
   $table->string('nombre_participant');
            $table->string('regles');
            $table->date('date_de_debut');
            $table->enum('periode',['hebdomaire','mensuel','quotidien','annuel']);
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




    if($tontine->statutTontine === 'accepte')
   {  
       For($i=1; $i <=$nbre_participantTontine + 1; $i++)
        {
            
           
            $cycles = new GestionCycle();
            $cycles -> tontine_id = $tontine->id;
            $cycles->date_cycle = carbon::now()->addDays($duree * ($i - 1));
            $cycles ->nombre_de_cycle = $i;
            $cycles->statut = $request->statut;

    //         $participantsList = [];
          
    foreach ($participationTontines as $participationTontine) {
        $participantsList[] = $participationTontine->toArray(); // Stocker chaque participant dans le tableau
    }
            $tontine->update(['etat'=>'en_cours']);

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
