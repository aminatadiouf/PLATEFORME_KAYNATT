<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
      

        return [
            'name'=>'required|string|max:255',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|min:4',

            'adresse' => 'required|string',
            'telephone' => ['required', 'regex:/^(77|78|76|70)\d{7}$/'],
            'telephone_d_un_proche' =>['required', 'regex:/^(77|78|76|70)\d{7}$/'],
            'num_carte_d_identite'=>['required','regex:/^[0-9]{13}$/'],
             
            // 'role'=>'required|in:participant_tontine',
          

           ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([

            'succes'=>'false',
            'error'=>'true',
            'message'=>'Erreurr de validation',
            'errorList'=>$validator->errors(),
        ]));
    }

    public function messages()
    {
        return[
        'name.required'=>'le name doit être fourni',
        'name.string'=>'le name doit être une chaîne de caractére',
        'name.max'=>'le name ne doit dépasser 255 caractéres',

        'email.required'=>'l\'email doit être fourni',
        'email.string'=>'l\'email doit être une chaîne de caractére',
        'email.email' => 'L\'email doit être une adresse email valide',
        'email.unique' => 'Cet email est déjà utilisé par un autre utilisateur.',

        'adresse.required' => 'L\'adresse doit être fourni',
        'adresse.string' => 'L\'adresse doit être une chaîne de caractères',

        'telephone.required'=>'le numéro de telephone doit être fourni',
        'telephone.regex'=>'le numéro de telephone doit commencer par ses chiffres 77|78|76|70 ',

        
        'telephone_d_un_proche.required'=>'le numéro de telephone d\'un proche  doit être fourni',
        'telephone_d_un_proche.regex'=>'le numéro de telephone doit commencer par ses chiffres 77|78|76|70 ',

        'num_carte_d_identite.required'=>'le numéro de la carte d\'identité doit être fourni',
        'num_carte_d_identite.regex'=>'le numéro de la carte d\'identité doit contenir 13 chiffres',


        'password.required' => 'Le mot de passe doit être fourni',
        'password.min' => 'Le mot de passe doit comporter au moins 4 caractères',

        // 'role.required'=>'le role doit être fourni',
        //  'role.participant_tontine' => 'Le role doit être  participant_tontine',
        
        
        ];
    }
}

