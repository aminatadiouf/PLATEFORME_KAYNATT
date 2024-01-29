<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\UserCreateRequest;

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







class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return'pk';
    }

 /**
     * Register a new user.
     *
     * @param  \App\Http\Requests\UserCreateRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *     path="/registerUser",
     *     summary="Inscription d'un utilisateur",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="adresse", type="string"),
     *             @OA\Property(property="num_carte_d_identite", type="string"),
     *             @OA\Property(property="telephone", type="string"),
     *             @OA\Property(property="telephone_d_un_proche", type="string"),
     *             @OA\Property(property="role", type="string", enum={"participant_tontine", "createur_tontine"})
        
     * )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur inscrit avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="status_message", type="string", example="vous vous êtes inscrits")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation des données"

     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur interne du serveur"
     * 
     *     )
     * )
     */



    public function register(UserCreateRequest $request)
    {
        
        try {
      
      
            $users = new User();

            $users->name = $request->name;
            $users->email = $request->email;
            $users->password = Hash::make($request->password);
            $users->adresse = $request->adresse;

            $users->num_carte_d_identite = $request->num_carte_d_identite;
            $users->telephone= $request->telephone;
            $users->telephone_d_un_proche= $request->telephone_d_un_proche;


            $users->role = $request->role;
          
                 $users->save();
       
            return response()->json([
                'status_code'=>200,
                'status_message'=>'vous vous êtes inscrits',
                'data'=>$users
            ]);
        } catch (Exception $e) {
           return response()->json($e);
        } 
    }



  /**
 * @OA\Post(
 *     path="/loginUser",
 *     operationId="loginUser",
 *     tags={"Users"},
 *     summary="Authentification utilisateur ",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful login",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="vous vous êtes connectés avec succès"),
 *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid details",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Invalid details")
 *         )
 *     )
 * )
 */

  

    public function login(Request $request)
    {
        try {
            //code...
      
        $request->validate([
            "email" => "required|string|email",
            "password" => "required|min:4"
        ]);

         // JWTAuth
         $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);

        if(!empty($token)){
        $user = Auth::user();


            return response()->json([
                "status" => true,

                "message" => "vous vous êtes connectés avec succés",
                'data'=>$user,
                "token" => $token
                
             
            ]);

        }
        return response()->json([
                "status" => false,
                "message" => "Invalid details"
            ]);
        } catch (Exception $e) {
        return response()->json($e)  ;
    }
        
    }

    /**
 * Logout user and invalidate the token.
 *
 * @return \Illuminate\Http\Response
 *
 * @OA\Post(
 *     path="/logoutUser",
 *     summary="Déconnexion de l'utilisateur",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="Déconnexion réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Déconnexion réussie")
 *         )
 *     )
 * )
 */


    public function logoutUser()
    {
            auth()->logout();
            return response()->json(['message déconnexion réussi']);
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

    public function touslesUtilisateurs(User $allUser)
    {
        try {
         
            $allUser = User::all();

            return response()->json([
                'status_code'=>200,
                'status_message'=>'la liste de tous les tontines',
                'data'=>$allUser,
            ]);
              
            } catch (Exception $e) {
                return response()->json($e);
            }
    }

    /**
     * Update the specified resource in storage.
     */

     /**
 * Update user information.
 *
 * @param  \App\Http\Requests\EditUserRequest  $request
 * @param  \App\Models\User  $users
 * @return \Illuminate\Http\Response
 *
 * @OA\Post(
 *     path="/modifierUser",
 *     summary="Mise à jour des informations de l'utilisateur que ce soit participant ou createur",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="password", type="string"),
 *             @OA\Property(property="adresse", type="string"),
 *             @OA\Property(property="num_carte_d_identite", type="string"),
 *             @OA\Property(property="telephone", type="string"),
 *             @OA\Property(property="telephone_d_un_proche", type="string"),
 *             @OA\Property(property="role", type="string", enum={"participant_tontine", "createur_tontine"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Informations de l'utilisateur mises à jour avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status_code", type="integer", example=200),
 *             @OA\Property(property="status_message", type="string", example="Les informations de l'utilisateur ont été modifiées avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erreur de validation des données"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur"
 *     )
 * )
 */

    public function update(EditUserRequest $request, User $users)
    {
        try {
      
      
            $users->name = $request->name;
            $users->email = $request->email;
            $users->password = Hash::make($request->password);
            $users->adresse = $request->adresse;

            $users->num_carte_d_identite = $request->num_carte_d_identite;
            $users->telephone= $request->telephone;
            $users->telephone_d_un_proche= $request->telephone_d_un_proche;


            $users->role = $request->role;
          
                 $users->save();
       
            return response()->json([
                'status_code'=>200,
                'status_message'=>'les informations cooncernant l\'utilisateur ont été modifiés avec succés',
                'data'=>$users
            ]);
        } catch (Exception $e) {
           return response()->json($e);
        }  
    }

    /**
     * Remove the specified resource from storage.
     */

     /**
 * Delete a user.
 *
 * @param  \App\Models\User  $users
 * @return \Illuminate\Http\Response
 *
 * @OA\Delete(
 *     path="/admin/supprimerUser",
 *     summary="Supprimer un utilisateur ",
 *     tags={"Admins"},
 *     security={{"jwt_token":{}}},
 *     @OA\Parameter(
 *         name="users",
 *         in="query",
 *         required=true,
 *         description="ID de l'utilisateur à supprimer",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Utilisateur supprimé avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status_code", type="integer", example=200),
 *             @OA\Property(property="status_message", type="string", example="L'utilisateur a été supprimé avec succès"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur interne du serveur",
 *     )
 * )
 */


    public function destroy(User $users)
    {
        try {
           
            if($users){
                   $users->delete();
   
                   
                   return response()->json([
                       'status_code'=>200,
                       'status_message'=>'la tontine a été supprimée avec succés',
                       'data'=>$users
                   ]);
               }
           } catch (Exception $e) {
               return response()->json($e);
           }
    }


        public function tontineparticipeParUser(User $user)

        {
            $tontineParticipe = User::FindOrFail($user->id);
            $tontineParticipe -> participacipationTontines;
            return response()->json([
                'status_code'=>200,
                'status_message'=>'la tontine a été supprimée avec succés',
                'data'=>$tontineParticipe
            ]);
        }
}
