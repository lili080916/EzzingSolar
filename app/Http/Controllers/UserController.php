<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreUserRequest;
use App\Rules\UniqueMailUser;
use App\Rules\Adult;

use App\Models\User;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function saveUser(StoreUserRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->birthday = $request->birthday;
        $user->save();

        return UserResource::make($user);
    }

    public function getUsers(Request $request)
    {
        $users = User::paginate(5);

        return UserCollection::make($users);
    }

    public function getUserById(Request $request, $id)
    {
        $user = User::find($id);

        return UserResource::make($user);
    }

    public function editUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'min:3'],
            'surname' => ['required', 'min:3'],
            'email' => ['required', 'email', new UniqueMailUser($id)],
            'birthday' => ['required', 'date', new Adult]
        ]);

        if ($validator->fails()) {
            return response(['data' => $validator->errors()], 433);
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->password = $request->password ? Hash::make($request->password) : $user->password;
        $user->birthday = $request->birthday;
        $user->save();

        return UserResource::make($user);
    }

    public function getUsersWithComments(Request $request)
    {
        $users = User::with(['comments'])->paginate(5);

        return UserCollection::make($users);
    }
}
