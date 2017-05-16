<?php

namespace App\Http\Controllers\Profile;

use App\Http\Requests\UpdateUserGeneral;
use App\Http\Requests\UpdateUserPassword;
use App\Http\Requests\DeleteUserAccount;

use App\Models\SocialAccount;
use App\Http\Controllers\Controller;

class ManageController extends Controller
{
    public function edit()
    {
        return view('users.edit.general');
    }

    public function saveGeneral(UpdateUserGeneral $request)
    {
        $user = $request->user();
        $user->fill($request->all());
        $user->save();

        return redirect('/you/edit')->with('success', 'You have successfully updated your profile');
    }

    public function password()
    {
         return view('users.edit.password');
    }

    public function savePassword(UpdateUserPassword $request)
    {
        $user = $request->user();
        $user->password = bcrypt($request->offsetGet('password'));
        $user->save();

        return redirect('/you/edit/password')->with('success', 'You have successfully updated your password');
    }

    public function delete()
    {
        return view('users.edit.delete');
    }

    public function saveDelete(DeleteUserAccount $request)
    {
        $request->user()->delete();
        auth()->logout();
        return redirect('/profiles/auth')->with('success', 'We have successfully deleted your account');
    }

    public function thirdparty()
    {
        session(['_previous' => redirect()->back()]);
        return view('users.edit.thirdparty');
    }

    public function revokeThirdParty()
    {
        $account = SocialAccount::findOrFail(request()->offsetGet("id"));
        $account->delete();

        return redirect('/you/edit/thirdparty')->with('success', ucfirst($account->provider) . ' was revoked from your account');
    }

    public function api()
    {
        return view('users.edit.api');
    }
}
