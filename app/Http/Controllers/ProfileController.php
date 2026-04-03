<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    private const NAME_REGEX = '/^[a-zA-Z0-9_]+$/';
    private const EMAIL_REGEX = '/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/';
    private const PASSWORD_REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{6,30}$/';

    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:4',
                'max:16',
                'regex:' . self::NAME_REGEX,
                Rule::unique('users', 'name')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'regex:' . self::EMAIL_REGEX,
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:2048',
            ],
            'remove_avatar' => ['nullable', 'boolean'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => [
                'nullable',
                'string',
                'min:6',
                'max:30',
                'confirmed',
                'regex:' . self::PASSWORD_REGEX,
            ],
        ], [
            'name.regex' => 'Nickname can only contain letters, numbers, and underscores.',
            'email.regex' => 'Please enter a valid email address.',
            'password.regex' => 'Password must include uppercase, lowercase, number, and special character.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be a JPG, PNG, GIF, or WebP file.',
            'avatar.max' => 'Avatar must be smaller than 2MB.',
            'current_password.current_password' => 'Current password does not match your account.',
        ], [
            'name' => 'nickname',
        ]);

        $emailChanged = $validated['email'] !== $user->email;

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar') && $user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $validated['avatar'] = null;
        } else {
            unset($validated['avatar']);
        }

        unset($validated['remove_avatar'], $validated['current_password']);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        if ($emailChanged) {
            $validated['email_verified_at'] = null;
        }

        $user->update($validated);

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return redirect()
            ->route('profile.edit')
            ->with('message', $emailChanged
                ? 'Profile updated. Please verify your new email address.'
                : 'Profile updated successfully.');
    }
}
