<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditTontineRequest extends FormRequest
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

     /*
     
     'libelle',
    'description',
    'montant',
    'nombre_participant',
    'regles',
    'date_de_debut',
    'periode',
    'etat'
     
     */
    public function rules(): array
    {
      

        return [
            'libelle'=>'required|string|max:55|',
            'description'=>'required|string|',
            'montant'=>'required|integer',

            'nombre_participant' => 'required|integer',
            'regles' => 'required|',
            'date_de_debut' => 'required|date',
            'periode'=>'required|in:hebdomaire,mensuel,quotidien,annuel', 
            'etat'=>'required|in:en_attente,en_cours,termine',
            'statutTontine'=>'required|in:en_attente,accepte,refuse',
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
        'libelle.required'=>'la libelle doit être fourni',
        'libelle.string'=>'la libelle doit être une chaîne de caractére',
        'libelle.max'=>'la libelle ne doit dépasser 55 caractéres',
        'libelle.unique' => 'Cette libelle est déjà utilisé par un autre createur.',


        'description.required'=>'la description doit être fourni',
        'description.string'=>'la description doit être une chaîne de caractére',
        
        'montant.required'=>'le montant doit être fourni',
        'montant.integer'=>'le montant doit être une chaîne de caractére',

        
        'nombre_participant.required'=>'le nombre de participant doit être fourni',
        'nombre_participant.integer'=>'le nombre de participant  doit être un nombre',


        'regles.required' => 'Les régles doivent être fournis',

        
        'date_de_debut.required'=>'la date de début doit être fourni',
        'date_de_debut.date'=>'une date doit être fourni pour la date de début',



        'periode.required'=>'la periode  doit être fourni',
        'periode.in' => 'La periode doit être l\'un des suivants : hebdomaire,mensuel,quotidien,annuel',

     
        'etat.required'=>'l\'etat doit être fourni',
        'etat.in' => 'l\'etat doit être l\'un des suivants : en_attente,en_cours,termine',

        'statutTontine.required'=>'le statut doit être fourni',
        'statutTontine.en_attente' => 'Le statut doit être l\'un des suivants : en_attente,accepte,refuse',

        ];
    }
}

