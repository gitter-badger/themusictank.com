<?php

namespace App\Http\Requests;

class DeleteUserAccount extends UserManagement
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'confirm' => 'regex:/^delete\smy\saccount$/',
        ];
    }
}
