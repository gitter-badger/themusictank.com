<?php

namespace App\Http\Requests;

class UpdateUserGeneral extends UserManagement
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validations = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'slug' => 'required'
        ];

        $user = $this->user();
        $user->fill($this->all());

        if ($user->isDirty('email')){
            $validations['email'] .= "|unique:users";
        }

        if ($user->isDirty('slug')){
            $validations['slug'] .= "|unique:users";
        }

        return $validations;
    }
}
