<?php

namespace App\Http\Controllers\Api;

//use App\Models\Payment;
use App\Models\Payment;
use function Ramsey\Uuid\v1;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Services\PaytechService;
use Illuminate\Support\Facades\Redirect;


class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */

     public function index($price, $participation_Tontine_id, $gestion_cycle_id)

     { 
        

    return view('index', compact( 'price', 'gestion_cycle_id','participation_Tontine_id'));
}





public function payment(PaymentRequest $request)
{
   // $validated = $request->validated();



    // send info to api paytech
    $IPN_URL = 'https://urltowebsite.com';

    // $amount = $validated['price'];
    // $participation_Tontine_id = $validated['participation_Tontine_id'];
    $amount = $request->input('price');
   $participation_Tontine_id = $request->input('participation_Tontine_id');
    $gestion_cycle_id = $request->input('gestion_cycle_id');

 

    $code = "47"; // This can be the product id

    $success_url = route('payment.success', [
        'code' => $code,
        'data' => [
            'amount' => $request->price,
           'participation_Tontine_id' => $participation_Tontine_id,
            'gestion_cycle_id' => $gestion_cycle_id,
        ],

        'gestion_cycle_id' => $gestion_cycle_id,

    ]);

    // The success_url takes two parameters: the first one can be product id and the other all data retrieved from the form

    $cancel_url = route('payment.index');
    $paymentService = new PaytechService(config('paytech.PAYTECH_API_KEY'), config('paytech.PAYTECH_SECRET_KEY'));

    $jsonResponse = $paymentService->setQuery([
        'item_price' => $amount,
       'participation_Tontine_id' => $participation_Tontine_id,
        'gestion_cycle_id' => $gestion_cycle_id,
        'command_name' => "Votre paiement tontine a été effectué avec succès",
    ])
        ->setCustomeField([
            'time_command' => time(),
            'ip_user' => $_SERVER['REMOTE_ADDR'],
            'lang' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
        ])
        ->setTestMode(true) // Change it to false if you are turning in production
        ->setCurrency("xof")
        ->setRefCommand(uniqid())
        ->setNotificationUrl([
            'ipn_url' => $IPN_URL . '/ipn',
            'success_url' => $success_url,
            'cancel_url' => $cancel_url,
        ])->send();

        if ($jsonResponse['success'] < 0) {
            // return back()->withErrors($jsonResponse['errors'][0]);
            return 'error';
        } elseif ($jsonResponse['success'] == 1) {
            // Redirection to Paytech website for completing checkout
            $token = $jsonResponse['token'];
            session(['token' => $token]);


        // $montantDejaCotise = Payment::where('gestion_cycle_id', $gestion_cycle_id)
        //     ->sum('amount');
        //      $payment = new Payment();
            

        // $payment->amount = $amount;
        // $payment->participation_Tontine_id = $participation_Tontine_id;
        // $payment->gestion_cycle_id = $gestion_cycle_id;
        // $payment->statutCotisation = 'cotise';
        // $payment->montant_a_gagner = $montantDejaCotise + $amount;

        // $payment->save();
        return redirect($jsonResponse['redirect_url']);

        }
}



    public function success(Request $request, $code)
    {
        $data = $request->query('data');
      
        $gestion_cycle_id = $data['gestion_cycle_id'];

        $montantDejaCotise = Payment::where('gestion_cycle_id', $gestion_cycle_id)
            ->sum('amount');
        
        $token = session('token') ?? '';
      

        if (!$token || !$data) {
            return redirect()->route('payment.index')->withErrors('Token ou données manquants');
        }

        $data['token'] = uniqid(); // Utiliser uniqid() pour générer un token unique

    $payment = new Payment([
        'token' => $data['token'],
    
        'amount' => $data['amount'],
        'participation_Tontine_id' => $data['participation_Tontine_id'],
        'gestion_cycle_id' => $data['gestion_cycle_id'],
        'statutCotisation' => 'cotise',
        
       
'montant_a_gagner'=>$montantDejaCotise +  $data['amount'],

    ]);
        $payment->save();

        if (!$payment) {
            return redirect()->route('payment.index')->withErrors('Échec de la sauvegarde du paiement');
        }

        session()->forget('token');

        return view('succes');
    }

   


    public function paymentSuccessView(Request $request, $code)
    {
        // You can fetch data from db if you want to return the data to views

        /* $record = Payment::where([
            ['token', '=', $code],
            ['user_id', '=', auth()->user()->id]
        ])->first(); */

        return view('vendor.paytech.success'/* , compact('record') */)->with('success', 'Félicitation, Votre paiement est éffectué avec succès');
    }

    public function cancel()
    {
        # code...
    }
}