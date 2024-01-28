<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Admin;
use App\Models\Tontine;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AdminCreateRequest;
use App\Notifications\AccepteCreationTontine;

class AdminController extends Controller
{


    public function registerAdmin(AdminCreateRequest $request)
    {
        
       
        try {
      
      
            $admins = new Admin();

            $admins->name_admin = $request->name_admin;
            $admins->email_admin = $request->email_admin;
            $admins->password = Hash::make($request->password);

            $admins->save();
       
            return response()->json([
                'status_code'=>200,
                'status_message'=>'vous vous êtes inscrits en tant que admin',
                'data'=>$admins
            ]);
        } catch (Exception $e) {
           return response()->json($e);
        } 
    }


    public function loginAdmin(Request $request)
{
    try {
        //code...
   
    $credentials = $request->only('email_admin', 'password');

    if (!$token = Auth::guard('admin_api')->attempt($credentials)) {
        return response()->json(['message' => 'Invalid email or password'], 401);
    }

        return response()->json([
            'status_message'=>'vous vous êtes connectés avec succés',
                
                'token' => $token]);
    } catch (Exception $e) {
    return response()->json($e);
    }
    }

    public function logoutAdmin()
    {
        auth()->logout();
        return response()->json(['message déconnexion réussi']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return 'okay';
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
    public function store(Request $request)
    {
        //
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


    // public function AccepteDemandeTontine(Tontine $tontines)

    // {
    //     try {
    //         $dataTontine =Tontine ::find($tontines);
            
    //        // $role = Role::where('name', 'createur_tontine')->get();
    
    //         if (!$dataTontine ) {
    //             return response()->json([ 
    //                "status" => false,
    //                "message" => "la tontine n'existe pas "
    //             ]);
               
    //         }
    
    //         if ($dataTontine->statutTontine === 'accepte') {
    //             return response()->json([ 
    //                 "status" => false,
    //                 "message" => "la tontine a déjà été acceptée "
    //             ]);
    //         }


    //         $dataTontine->statutTontine->update(['tontines' => 'accepte']);
    //         $dataTontine->save();
    
    //         $dataTontine->user()->notify(new AccepteCreationTontine());
    //         return response()->json([ 
    //             "status" => true,
    //             "message" => "la demande de création de tontine a été acceptée",
    //             "data"=>$dataTontine
    //         ]);
    //     } catch (Exception $e) {
    //         return response()->json($e);
    //     }
    // }
}
