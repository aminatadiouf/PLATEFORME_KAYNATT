<?php

namespace App\Http\Controllers\api;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\CotisationTontine;
use App\Http\Controllers\Controller;
use App\Models\ParticipationTontine;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\PaytechService;
use App\Http\Requests\PayementRequest;
use Illuminate\Support\Facades\Redirect;


class PayementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */

    public function index(ParticipationTontine $participationTontines)
    {
        $participationTontine = ParticipationTontine::FindOrFail($participationTontines->id);

        return view('index',compact('participationTontine'));
    }

    public function payment(Request $request,CotisationTontine $cotisations,ParticipationTontine $participationTontines)
    {
        # send info to api paytech

        // $validated = $request->validated();
        $IPN_URL = 'https://urltowebsite.com';

        
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


        $cycle = $participationTontine->tontine->gestion_cycles()
        ->orderBy('id', 'asc')
            ->first()
            ->id;

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
                    'statut_message'=> 'l\'utilisateur n\'est pas un participant de la tontine ',
                   
                ]);
            }
       if($participationTontine->statutParticipation !='accepte')
       {
        return response()->json([
            'statut_code'=> false,
            'statut_message'=> 'l\'utilisateur n\'est pas encore accepté à participer à la tontine  ',
           
        ]);
       }
// dd(       $participationTontine->tontine->etat ==='en_cours')
// ;
            
       if ($participationTontine->user_id === $user->id &&  $participationTontine->statutParticipation ==='accepte' &&
       $participationTontine->tontine->etat ==='en_cours')

      {

       
      // $cotisations ->date_paiement = $request->date_paiement;
       $cotisations->montant_paiement = $request->montant_paiement;
       $cotisations->participation_Tontine_id = $user->id;
       $cotisations->gestion_cycle_id = $cycle;








        // // $amount = $validated['price'] * $validated['qty'];
        // $amount = $request['price'] * $request['qty'];
        // $code = "47"; // This can be the product id

        /*
        The success_url take two parameters, the first one can be product id and
        the one all data retrieved from the form
        */

        $success_url = route('payment.success', ['code' =>$participationTontine->user_id, 'data' => ($request)->all()]);
        $cancel_url = route('payment.index');
        $paymentService = new PaytechService(config('paytech.PAYTECH_API_KEY'), config('paytech.PAYTECH_SECRET_KEY'));


        $jsonResponse = $paymentService->setQuery([
            $jsonResponse = $paymentService->setQuery([
                'item_name' => "Cotisation",
                'item_price' => $request->montant_paiement,
                'command_name' => "Paiement pour une cotisation de tontine via PayTech",
            ])
        ])
            ->setCustomeField([
                'item_id' =>$participationTontine->user_id, // You can change it by the product id
                'time_command' => time(),
                'ip_user' => $request->ip(),
                'lang' => $_SERVER['HTTP_ACCEPT_LANGUAGE']
            ])

            /*
 ->setCustomeField([
            'item_id' => $collecteId,
            'time_command' => time(),
            'ip_user' => $request->ip(),
            'lang' => $request->server('HTTP_ACCEPT_LANGUAGE')
        ])
            */
            ->setTestMode(true) // Change it to false if you are turning in production
            ->setCurrency("xof")
            ->setRefCommand(uniqid()) // You can add the invoice reference to save it to your paytech invoices
            ->setNotificationUrl([
                'ipn_url' => $IPN_URL . '/ipn', //only https
                'success_url' => $success_url,
                'cancel_url' => $cancel_url
            ])->send();
        // dd($request->all());


        if ($jsonResponse['success'] < 0) {
            return back()->withErrors($jsonResponse['errors'][0]);
        } elseif ($jsonResponse['success'] == 1) {
            # Redirection to Paytech website for completing checkout
            $token = $jsonResponse['token'];
            session(['token' => $token]);
            return Redirect::to($jsonResponse['redirect_url']);
        }
    }
}

    public function success(Request $request, $code)
    {
        $validated = $_GET['data'];
        $validated['token'] = session('token') ?? '';

        // Call the save methods to save data to database using the Payment model

        $payment = $this->savePayment($validated);

        session()->forget('token');

        return Redirect::to(route('payment.success.view', ['code' => $code]));
    }

    public function savePayment($data = [])
    {

        # save payment database

        $payment = Payment::firstOrCreate([
            'token' => $data['token'],
            'montant_paiement'=>$data['montant_paiement'],
            'participation_Tontine_id'=>$data['participation_Tontine_id'],
            'gestion_cycle_id'=>$data['gestion_cycle_id']
        ], [
            
        ]);

        if (!$payment) {
            # redirect to home page if payment not saved
            return $response = [
                'success' => false,
                'data' => $data
            ];
        } 


        # Redirect to Success page if payment success

        $data['payment_id'] = $payment->id;

        /*
            You can continu to save onother records to database using Eloquant methods
            Exemple: Transaction::create($data);
        */

        return $response = [
            'success' => true, //
            'data' => $data
        ];
    }

    public function paymentSuccessView(Request $request, $code)
    {
        // You can fetch data from db if you want to return the data to views

        /* $record = Payment::where([
            ['token', '=', $code],
            ['user_id', '=', auth()->user()->id]
        ])->first(); */

        return view('vendor.paytech.success' /* , compact('record') */)->with('success', 'Félicitation, Votre paiement est éffectué avec succès');
    }

    public function cancel()
    {
        # code...
    }
}