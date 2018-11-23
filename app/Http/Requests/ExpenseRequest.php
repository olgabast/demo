<?php

namespace App\Http\Requests;

use Dingo\Api\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    /**
     * Only owner can change expenses
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'datetime' => 'required|date',
            'description' => 'max:255',
            'amount' => 'required|numeric',
            'comment' => 'max:255'
        ];
    }
}
