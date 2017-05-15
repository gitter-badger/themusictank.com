<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ManageController extends Controller
{

    public function edit()
    {
        return view('users.edit.general');
    }

    public function thirdparty()
    {
        return view('users.edit.thirdparty');
    }

    public function api()
    {
        return view('users.edit.api');
    }

    public function password()
    {
        return view('users.edit.password');
    }

    public function delete()
    {
        return view('users.edit.delete');
    }

    public function generalPost()
    {
        $user = auth()->user();
        $user->fill(request()->all());

        $this->generalvalidator(request()->all())->validate();

        $user->save();

        return redirect('/you/edit');
    }


    protected function generalvalidator($data)
    {
        $validations = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
            'slug' => 'required'
        ];

        if ($user->isDirty('email')){
            $validations['email'] .= "|unique:users";
        }

        if ($user->isDirty('slug')){
            $validations['slug'] .= "|unique:users";
        }

        return Validator::make($data, $validations);
    }

    public function thirdpartyPost()
    {

    }

    public function deletePost()
    {
        return view('users.edit.delete');
    }

    public function passwordPost()
    {
        return view('users.edit.delete');
    }
}
