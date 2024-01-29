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


class AdminController extends Controller
{


    public function registerAdmin(AdminCreateRequest $request)
    {
        
       
        try {
      
      
            $admins = new Admin();

            $admins->name_admin = $request->name_admin;
            $admins->email_admin = $request->email_admin;
            $admins->password = Hash::make($request->password);
            $admins->role = $request->role;
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
/**
 * Login as an admin and obtain a token.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 *
 * @OA\Post(
 *     path="/loginAdmin",
 *     summary="Connexion en tant qu'administrateur",
 *     tags={"Admins"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email_admin", "password"},
 *             @OA\Property(property="email_admin", type="string", example="admin@example.com"),
 *             @OA\Property(property="password", type="string", example="password")
 *  *          @OA\Property(property="role", type="string", example="admin")

 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Connexion réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status_message", type="string", example="vous vous êtes connecté avec succès"),
 *             @OA\Property(property="token", type="string", example="JWT token")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Email ou mot de passe incorrect"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur"
 *     )
 * )
 */


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
/**
 * Logout the admin.
 *
 * @return \Illuminate\Http\Response
 *
 * @OA\Post(
 *     path="/logoutAdmin",
 *     summary="Déconnexion de l'administrateur",
 *     tags={"Admins"},
 *     @OA\Response(
 *         response=200,
 *         description="Déconnexion réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Déconnexion réussie")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur"
 *     )
 * )
 */

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
