<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => ['image','max:2048'],
            ]);
            $file = $request->file('photo');
            $dir = public_path('profile_photos');
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            $name = 'user-'.$user->id.'-'.time().'.'.$ext;
            // Delete previous photo to avoid multiple avatars
            $old = (string)($user->photo_path ?? '');
            if ($old !== '') {
                $oldAbs = public_path($old);
                if (is_file($oldAbs)) @unlink($oldAbs);
            }
            $file->move($dir, $name);
            $user->photo_path = 'profile_photos/'.$name;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
