<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // Mostra il profilo dell'utente loggato
    public function show()
    {
        return response()->json([
            'data' => Auth::user(),
            'message' => 'Profilo recuperato con successo'
        ], 200);
    }

    // Aggiorna il profilo
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->fill($request->only('name', 'email'));

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json([
            'data' => $user,
            'message' => 'Profilo aggiornato con successo'
        ], 200);
    }

    // Aggiorna la foto profilo
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');

            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->update([
                'profile_photo_path' => $path,
            ]);
        }

        return response()->json([
            'data' => $user,
            'message' => 'Foto profilo aggiornata con successo'
        ], 200);
    }

    // Elimina l'account dell'utente loggato
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return response()->json([
            'message' => 'Account eliminato con successo'
        ], 200);
    }
}
