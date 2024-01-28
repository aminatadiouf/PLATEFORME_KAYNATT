<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ParticipationCreateRequest extends FormRequest
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
            
                //'user_id'=>'required|',
                'tontine_id'=>'required|',
                'statutParticipation'=>'|en_attente,accepte,refuse',
                'date'=>'required|date'
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
        //'user_id.required'=>'l\'utilisateur doit être fourni',

        'tontine_id.required'=>'la tontine doit être fourni',

        'statutParticipation.required'=>'le statut doit être fourni',
        'statutParticipation.en_attente' => 'Le statut doit être l\'un des suivants : en_attente,accepte,refuse',


        'date.required'=>'la date doit être fourni',
        ];
    }
}


