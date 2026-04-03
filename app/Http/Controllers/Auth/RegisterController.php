<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'min:4',
                'max:16',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:users,name',
            ],
            'email' => [
                'required',
                'string',
                'max:255',
                'email',
                'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:30',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{6,30}$/',
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:2048',
            ],
        ], [
            'name.regex' => 'Nickname can only contain letters, numbers, and underscores.',
            'name.unique' => 'This nickname is already taken.',
            'name.min' => 'Nickname must be at least 4 characters.',
            'name.max' => 'Nickname must be at most 16 characters.',
            'email.unique' => 'An account with this email already exists.',
            'email.regex' => 'Please enter a valid email address.',
            'password.regex' => 'Password must include uppercase, lowercase, number, and special character.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.max' => 'Password must be at most 30 characters.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be a JPG, PNG, GIF, or WebP file.',
            'avatar.max' => 'Avatar must be smaller than 2MB.',
        ], [
            'name' => 'nickname',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $userRole = Role::where('name', 'user')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $userRole->id,
            'avatar' => $avatarPath,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
