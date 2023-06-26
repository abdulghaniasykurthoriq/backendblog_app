<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    // register user
    public function register(Request $request)
    {

        $attrs = $request->validate([
            'name' => 'required|string',
            'email' => 'email|required|string|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $attrs['name'],
            'email' => $attrs['email'],
            'password' => bcrypt($attrs['password'])
        ]);

        return response([
            'user' => $user,


            'token' => $user->createToken('secret')->plainTextToken
        ]);
    }


    // login user
    public function login(Request $request)
    {

        $attrs = $request->validate([
            'email' => 'email|required',
            'password' => 'required|min:6',
        ]);
        if (!Auth::attempt($attrs)) {
            return response([
                'message' => 'invalid credentials'
            ], 403);
        };
        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth-token')->plainTextToken;
        // auth()->createToken('secret')->plainTextToken
        return response([
            'user' => auth()->user(),
            'token' => $token
        ], 200);
    }

    // logout user
    public function logout(Request $request)
    {
        // auth()->user()->tokens()->delete();
        $request->user()->tokens()->delete();
        return response([
            'message' => 'logout success'
        ], 200);
    }
    // update user
    public function update(Request $request)
    {
        $attrs = $request->validate([
            'name' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'profiles');

        $user = User::find(auth()->user()->id);

        // $user = auth()->user();
        // $user->name = $attrs['name'];
        // $user->image = $image;
        // $user->save();

        $user->update([
            'name' => $attrs['name'],
            'image' => $image
        ]);

        // auth()->user()->update([
        //     'name' => $attrs['name'],
        //     'image' => $image
        // ]);

        return response([
            'message' => 'User updated.',
            'user' => auth()->user()
        ], 200);
    }

    public function user()
    {
        return response([
            'user' => auth()->user()
        ], 200);
    }
}
