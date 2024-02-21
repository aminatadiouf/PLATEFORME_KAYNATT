<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Admin_test extends TestCase
{
   // use WithFaker; // Pour utiliser des données factices




   // use RefreshDatabase; // Pour réinitialiser la base de données après chaque test

    public function test_registerAdmin():void
    {
        $userData = [
            'name_admin' => 'jeanne diop',
            'email_admin' => 'jeanne@example.org',
             'password' => 'password',
             'role' => 'admin',
            
         ];
        $response = $this->postJson('api/registerAdmin', $userData);
        $response->assertStatus(200);
   
    }


    public function test_loginAdmin():void
    {

        $userData = [
            'email_admin'=>'jeanne@example.org',
            'password' => 'password',
        ];
        $response = $this->postJson('api/loginAdmin', $userData);
        $response->assertStatus(200);
    }



    public function test_logoutAdmin(): void
{
    $userData = [
        'name_admin' => 'sokhna diop',
        'email_admin'=>'sokhna@example.org',
        'password' => 'password',
        'role' => 'admin',
    ];
    $token = Auth::guard('admin_api')->attempt([
        'email_admin'=>'sokhna@example.org',
        'password' => 'password',
    ]);

    $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/logoutAdmin');

    $response->assertStatus(200);

    $response->assertJson(['message déconnexion réussi']);
 

}
}

