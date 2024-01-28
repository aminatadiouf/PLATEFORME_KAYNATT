<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\UserCreateRequest;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return'pk';
    }

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

            return response()->json([
                "status" => true,

                "message" => "vous vous êtes connectés avec succés",
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
