<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateCotisationTontineRequest extends FormRequest
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
     $table->id();
            $table->string('montant_paiement');
            $table->date('date_paiement');
    */
    public function rules(): array
    {
        return [
            
                'montant_paiement'=>'required|string|',
                'date_paiement'=>'required|date|',
                //'participationTontine_id'=>'required|',
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

            'montant_paiement.required'=>'le montant_paiement doit être fourni',
            'montant_paiement.string'=>'le montant_paiement doit être une chaîne de caractére',
    

            'date_paiement.required'=>'le date_paiement doit être fourni',
            'date_paiement.string'=>'le date_paiement doit être une chaîne de caractére',
        ];
    }
}
