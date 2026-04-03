<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function show(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', old('email')),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => [
                'required',
                'string',
                'max:255',
                'email',
                'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:30',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{6,30}$/',
            ],
        ], [
            'email.regex' => 'Please enter a valid email address.',
            'password.regex' => 'Password must include uppercase, lowercase, number, and special character.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.max' => 'Password must be at most 30 characters.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('message', __($status));
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput($request->only('email', 'token'));
    }
}
