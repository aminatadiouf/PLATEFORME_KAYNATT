<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class User_test extends TestCase
{

    /*
     'name',
        'email',
        'password',
        'adresse',
        'telephone',
        'num_carte_d_identite',
        'telephone_d_un_proche',
        'role'
    */

    public function test_registerUser():void
    {
        $userData = [
            'name' => 'jeanne diop',
            'email' => 'jeanne@example.org',
            'password' => 'password',
            'adresse'=>'keur massar ',
            'telephone'=>'774563663',
            'telephone_d_un_proche'=>'780255447',
            'num_carte_d_identite'=>'0123456789123',
            //'role'=>'participant_tontine',

         ];
        $response = $this->postJson('api/registerUser', $userData);
        $response->assertStatus(200);
    }


    
    public function test_loginUser():void
    {

        $userData = [
            'email_admin'=>'jeanne@example.org',
            'password' => 'password',
        ];
        $response = $this->postJson('api/loginUser', $userData);
        $response->assertStatus(200);
    }


    
    public function test_logoutUser(): void
    {
        $userData = [
            'name' => 'cheikh diop',
                'email' => 'cheikh@example.org',
                'password' => 'password',
                'adresse'=>'keur massar ',
                'telephone'=>'774563663',
                'telephone_d_un_proche'=>'780255447',
                'num_carte_d_identite'=>'0123456789123',
        ];
        $token = Auth::guard('api')->attempt([
            'email'=>'cheikh@example.org',
            'password' => 'password',
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/logoutUser');

        $response->assertStatus(200);

        $response->assertJson(['message déconnexion réussi']);
    

    }


    public function test_updateUser():void
    {
        $userData = [
            'name' => 'aadama diop',
                'email' => 'adamma@example.org',
                'password' => 'password',
                'adresse'=>'keur massar ',
                'telephone'=>'774563663',
                'telephone_d_un_proche'=>'780255447',
                'num_carte_d_identite'=>'0123456789123',
        ];

        $response = $this->postJson('api/registerUser', $userData);
        $response->assertStatus(200);
        $token = Auth::guard('api')->attempt([
            'email'=>'adamma@example.org',
            'password' => 'password',
        ]);
        $user = User::where('email', 'adamma@example.org')->first();


        // $newUserData = [
        //     'name' => 'fatou sall',
        //     'password' => 'password',
        //     'adresse' => 'Pikine',
        //     'telephone' => '777777777',
        //     'telephone_d_un_proche' => '708888888',
        //     'num_carte_d_identite' => '9876543210123',
        // ];

        // Envoyer une demande HTTP PATCH avec les nouvelles données et le jeton d'authentification
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
                         ->postJson('/api/auth/modifierUser/'. $user->id ,[
                            'name' => 'fatou sall',
                            'password' => 'password',
                            'adresse' => 'Pikine',
                            'telephone' => '777777777',
                            'telephone_d_un_proche' => '708888888',
                            'num_carte_d_identite' => '9876543210123',
                         ],[
                            'Authorization' => 'Bearer ' . $token]);

                         $response->assertStatus(200);

                         $response->assertJson(['status_message'=>'les informations concernant l\'utilisateur ont été modifiés avec succés']);


    }


    // public function test_destroyUser():void
    // {
    //     $userAdmin = [
    //         'name_admin' => 'rokhaya diop',
    //         'email_admin' => 'rokhaya@example.org',
    //          'password' => 'password',
    //          'role' => 'admin',
            
    //      ];
    //     $response = $this->postJson('api/registerAdmin', $userAdmin);
    //     $response->assertStatus(200);

    //     $token = Auth::guard('admin_api')->attempt([
    //         'email'=>'rokhaya@example.org',
    //         'password' => 'password',
    //     ]);
        
    //     $userAdmin = Admin::where('email', 'rokhaya@example.org')->first();

        // $newUserData = [
        //     'name' => 'fatou sall',
        //     'password' => 'password',
        //     'adresse' => 'Pikine',
        //     'telephone' => '777777777',
        //     'telephone_d_un_proche' => '708888888',
        //     'num_carte_d_identite' => '9876543210123',
        // ];
        // $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
        // ->postJson('/api/admin/modifierUser/'. $userAdmin->id ),[
        // ];

           
    
}
